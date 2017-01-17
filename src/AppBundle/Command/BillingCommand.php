<?php
/**
 *
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Aws\S3\S3Client;
use EmpireBundle\Utility\MimeType;
use EmpireBundle\Entity\ImageMedia;
use EmpireBundle\Entity\VideoMedia;
use EmpireBundle\Entity\ListPostMember;

/**
 * =
 *
 * @author Josh Murphy
 *
 * @todo Add configure help
 * @todo Document each request
 */
class BillingCommand extends ContainerAwareCommand
{
    /*
     * {@inheritdoc}
     */

    /**
* 
* 
     * configure
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    protected function configure()
    {
        $this
            ->setName('empire:media:migrate')
            ->setDescription('Reviews media from db to migrate to defined s3 bucket')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry Run')
            ->setHelp('TODO: Fill this in');
    }

    /**
     * {@inheritdoc}
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->dryRun = $this->input->getOption('dry-run');
        if ($this->dryRun) {
            $this->output->writeln('<info>Dryrun Enabled</info>');
        }
        $revenue = $this->migrateMedia();
        $this->output->writeln('<info>Command empire:media:migrate completed successfully.</info>');
    }

    /**
* 
* 
     * migrateMedia
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    protected function migrateMedia()
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->client = new Client();
        $this->s3 = new S3Client(
            [
            'version' => 'latest',
            'region' => 'us-west-2',
            'credentials' => [
                'key' => $this->getContainer()->getParameter('amazon_aws_key'),
                'secret' => $this->getContainer()->getParameter('amazon_aws_secret_key'),
            ]
            ]
        );
        $this->bucket = $this->getContainer()->getParameter('amazon_s3_bucket_name');
        //Article Posts
        $this->migrateArticlePosts();
        //Video Posts
        //$this->migrateVideoPosts();
        //List Posts
        //$this->migrateListPosts();
        //Montage Posts
        //$this->migrateMontagePosts();
    }

    /**
     *
     */
    private function migrateVideoPosts()
    {
        $posts = $this->em->getRepository('EmpireBundle:VideoPost')->findAll();
        foreach ($posts as $post) {
            if ($this->output->isVerbose()) {
                $this->output->writeln('<comment>Post: ' . $post->getId() . '</comment>');
            }
            //Hotlinked images in html
            $this->manageHTMLImages($post);
            //Lead
            //$this->manageLeadImage($post);
            //Cover
            $this->manageCoverImage($post);
            //FB Thumbs
            $this->manageFacebookThumbs($post);
            //Yahoo Thumbs
            $this->manageYahooThumbs($post);
        }
    }

    /**
     *
     */
    private function migrateArticlePosts()
    {
        $posts = $this->em->getRepository('EmpireBundle:ArticlePost')->findAll();
        foreach ($posts as $post) {
            if ($this->output->isVerbose()) {
                $this->output->writeln('<comment>Post: ' . $post->getId() . '</comment>');
            }
            //Hotlinked images in html
            $this->manageHTMLImages($post);
            //Lead
            $this->manageLeadImage($post);
            //LeadOG
            $this->manageLeadOGImage($post);
            //Cover
            $this->manageCoverImage($post);
            //FB Thumbs
            $this->manageFacebookThumbs($post);
            //Yahoo Thumbs
            $this->manageYahooThumbs($post);
        }
    }

    /**
* 
* 
     * migrateListPosts
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    private function migrateListPosts()
    {
        $posts = $this->em->getRepository('EmpireBundle:ListPost')->findAll();
        //[],[], 1000, 1000);
        foreach ($posts as $post) {
            if ($this->output->isVerbose()) {
                $this->output->writeln('<comment>Post: ' . $post->getId() . '</comment>');
            }
            //Lead
            $this->manageLeadImage($post);
            //Cover
            $this->manageCoverImage($post);
            //FB Thumbs
            $this->manageFacebookThumbs($post);
            //Yahoo Thumbs
            $this->manageYahooThumbs($post);
            //HTML Content Images
            $this->manageHTMLImages($post);
            $lpms = $post->getListPostMembers();
            foreach ($lpms as $lpm) {
                /* if (!is_null($lpm->getMedia())) {
                  $this->output->writeln('.');
                  continue;
                  } */
                switch ($lpm->getMemberType()) {
                case ListPostMember::IMAGE: //Image
                    $url = 'http://images.guff.com/gallery/image/' . $lpm->getMemberData();
                    if ($this->output->isVerbose()) {
                        $this->output->writeln('<comment>List Image: ' . $url . '</comment>');
                    }
                    $valid = false;
                    $filename_from_url = parse_url($url);
                    $ext = pathinfo(
                        $filename_from_url['path'],
                        PATHINFO_EXTENSION
                    );
                    if ($ext == '') {
                        $ext = 'jpg';
                    }
                    $basename = pathinfo(
                        $filename_from_url['path'],
                        PATHINFO_FILENAME
                    );
                    $filename = 'media/' . $post->getId() . '/' . $basename . '.' . $ext;
                    if ($this->s3->doesObjectExist($this->bucket, $filename)) {
                        $this->output->writeln('<comment>List Post Image Exists: ' . $filename . '</comment>');
                        //continue;
                    } else {
                        try {
                            $response = $this->client->get(
                                $url,
                                ['stream' => true]
                            );
                            $file = $response->getBody()->getContents();
                            $valid = true;
                            //$this->output->writeln($this->bucket.'/gallery/image/'.$lpm->getMemberData());
                            $valid = $this->uploadImage(
                                $filename, $file,
                                $ext
                            );
                        } catch (\Exception $e) {
                            $this->output->writeln('<error>List Post Image not found: ' . $url . ' not found</error>');
                        }
                    }
                    if (!$valid) {
                        continue;
                    }
                    //$valid = $this->uploadImage($filename, $file, $ext);

                    $image = new ImageMedia();
                    $image->setUrl($filename);
                    $image->setAuthor($post->getAuthor());
                    $image->setPost($post);
                    $image->setSite($post->getSite());
                    $image->setStatus(1);
                    $image->setCaption($lpm->getCaption());
                    $image->setSource($lpm->getMemberSource());
                    $this->em->persist($image);
                    $lpm->setMedia($image);
                    $this->em->persist($lpm);
                    $this->em->flush();
                    break;
                case ListPostMember::YOUTUBE://Youtube
                    if ($this->output->isVerbose()) {
                        $this->output->writeln('<comment>List Youtube Video: ' . $lpm->getMemberData() . '</comment>');
                    }
                    $video = new VideoMedia();
                    $video->setUrl($lpm->getMemberData());
                    $video->setVideoType(0);
                    $video->setAuthor($post->getAuthor());
                    $video->setPost($post);
                    $video->setSite($post->getSite());
                    $video->setCaption($lpm->getCaption());
                    $video->setStatus(1);
                    $this->em->persist($video);
                    $lpm->setMedia($video);
                    $this->em->persist($lpm);
                    $this->em->flush();
                    break;
                case ListPostMember::GUFF_VIDEO://Guff Video
                    if ($this->output->isVerbose()) {
                        $this->output->writeln('<comment>List Guff Video: ' . $lpm->getMemberData() . '</comment>');
                    }
                    $video = new VideoMedia();
                    $video->setUrl($lpm->getMemberData());
                    $video->setVideoType(1);
                    $video->setAuthor($post->getAuthor());
                    $video->setPost($post);
                    $video->setSite($post->getSite());
                    $video->setCaption($lpm->getCaption());
                    $video->setStatus(1);
                    $this->em->persist($video);
                    $lpm->setMedia($video);
                    $this->em->persist($lpm);
                    $this->em->flush();
                    break;
                }
            }
        }
    }

    /**
* 
* 
     * migrateMontagePosts
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    private function migrateMontagePosts()
    {
        $posts = $this->em->getRepository('EmpireBundle:MontagePost')->findAll();
        foreach ($posts as $post) {
            if ($this->output->isVerbose()) {
                $this->output->writeln('<comment>Post: ' . $post->getId() . '</comment>');
            }
            //Lead
            $this->manageLeadImage($post);
            //Cover
            $this->manageCoverImage($post);
            //FB Thumbs
            $this->manageFacebookThumbs($post);
            //Yahoo Thumbs
            $this->manageYahooThumbs($post);
            //HTML Content Images
            $this->manageHTMLImages($post);
            $mpms = $post->getMontagePostMembers();
            foreach ($mpms as $mpm) {
                if (!is_null($mpm->getMedia())) {
                    continue;
                }
                switch ($mpm->getMemberType()) {
                case ListPostMember::IMAGE: //Image
                    $url = 'http://images.guff.com/gallery/image/' . $mpm->getMemberData();
                    if ($this->output->isVerbose()) {
                        $this->output->writeln('<comment>Montage Image: ' . $url . '</comment>');
                    }
                    $filename_from_url = parse_url($url);
                    $ext = pathinfo(
                        $filename_from_url ['path'],
                        PATHINFO_EXTENSION
                    );
                    if ($ext == '') {
                        $ext = 'jpg';
                    }
                    $basename = pathinfo(
                        $filename_from_url['path'],
                        PATHINFO_FILENAME
                    );
                    $filename = 'media/' . $post->getId() . '/' . $basename . '.' . $ext;
                    if ($this->s3->doesObjectExist($this->bucket, $filename)) {
                        $this->output->writeln('<comment>Montage Image Exists: ' . $filename . '</comment>');
                        continue;
                    }
                    try {
                        /* $response = $this->client->get($url,
                        ['stream' => true]);
                        $file     = $response->getBody()->getContents(); */
                        $valid = $this->copyImage(
                            $filename,
                            $this->bucket . '/gallery/image/' . $mpm->getMemberData(),
                            $ext
                        );
                    } catch (\Exception $e) {
                        $this->output->writeln('<error>Montage Post Image not found: ' . $url . ' not found</error>');
                        continue;
                    }
                    //$valid = $this->uploadImage($filename, $file, $ext);

                    $image = new ImageMedia();
                    $image->setUrl($filename);
                    $image->setAuthor($post->getAuthor());
                    $image->setPost($post);
                    $image->setSite($post->getSite());
                    $image->setSource($mpm->getMemberSource());
                    $image->setCaption($mpm->getCaption());
                    $image->setStatus(1);
                    $this->em->persist($image);
                    $mpm->setMedia($image);
                    $this->em->persist($mpm);
                    $this->em->flush();

                    break;
                case ListPostMember::YOUTUBE://Youtube
                    if ($this->output->isVerbose()) {
                        $this->output->writeln('<comment>Montage Youtube Video: ' . $mpm->getMemberData() . '</comment>');
                    }
                    $video = new VideoMedia();
                    $video->setUrl($mpm->getMemberData());
                    $video->setVideoType(0);
                    $video->setAuthor($post->getAuthor());
                    $video->setPost($post);
                    $video->setSite($post->getSite());
                    $video->setCaption($mpm->getCaption());
                    $video->setStatus(1);
                    $this->em->persist($video);
                    $mpm->setMedia($video);
                    $this->em->persist($mpm);
                    $this->em->flush();
                    break;
                case ListPostMember::GUFF_VIDEO://Guff Video
                    if ($this->output->isVerbose()) {
                        $this->output->writeln('<comment>Montage Guff Video: ' . $mpm->getMemberData() . '</comment>');
                    }
                    $video = new VideoMedia();
                    $video->setUrl($mpm->getMemberData());
                    $video->setVideoType(1);
                    $video->setAuthor($post->getAuthor());
                    $video->setPost($post);
                    $video->setSite($post->getSite());
                    $video->setCaption($mpm->getCaption());
                    $video->setStatus(1);
                    $this->em->persist($video);
                    $mpm->setMedia($video);
                    $this->em->persist($mpm);
                    $this->em->flush();
                    break;
                }
            }
        }
    }

    /**
     *
     * @param type $post
     * @return type
     */
    private function manageHTMLImages($post)
    {
        $html = $post->getHtml();
        if ($html == '') {
            return;
        }
        preg_match_all('/src="([^"]+)"/', $html, $imagereg);
        foreach ($imagereg[1] as $image) {
            $valid = false;

            //Image is already on the new cdn bucket
            //if (stristr($image, 'http://images.guff.com/media/' . $post->getId())) continue;
            if ($this->output->isVerbose()) {
                $this->output->writeln('<comment>Thumbnail: ' . $image . '</comment>');
            }
            $filename_from_url = parse_url($image);
            $ext = pathinfo(
                $filename_from_url['path'],
                PATHINFO_EXTENSION
            );
            $basename = pathinfo(
                $filename_from_url['path'],
                PATHINFO_FILENAME
            );
            if ($ext == '') {
                $ext = 'jpg';
            }
            $filename = 'media/' . $post->getId() . '/' . $basename . '.' . $ext;
            $new = '/' . $filename;
            if ($this->s3->doesObjectExist($this->bucket, $filename)) {
                $this->output->writeln('<comment>Image Exists: ' . $filename . '</comment>');
                $valid = true;
            } else {
                try {
                    $response = $this->client->get($image, ['stream' => true]);
                    $file = $response->getBody()->getContents();
                    $valid = $this->uploadImage($filename, $file, $ext);

                    if ($this->output->isVerbose()) {
                        $this->output->writeln('<comment>new: ' . $new . '</comment>');
                    }
                    $valid = true;
                } catch (\Exception $e) {
                    $this->output->writeln('<error>Post:' . $post->getId() . ' Thumbnail: ' . $image . ' not found</error>');
                }
            }
            if ($valid) {
                //Do we have an entity for this media?
                $imageM = $this->em->getRepository('EmpireBundle:ImageMedia')->findOneBy(['url' => $new]);
                if (!$imageM) {
                    $imageM = new ImageMedia();
                    $imageM->setUrl($new);
                    $imageM->setAuthor($post->getAuthor());
                    $imageM->setPost($post);
                    $imageM->setSite($post->getSite());
                    $imageM->setStatus(1);
                    $imageM->setSource($image);
                    $this->em->persist($imageM);
                }
                $html = str_replace($image, 'http://images.guff.com'.$new, $html);
                $post->setHtml($html);
                $this->em->persist($post);
                $this->em->flush();
            }
        }
    }

    /**
     *
     * @param type $post
     * @return type
     */
    private function manageLeadImage($post)
    {
        $content = $post->getLeadContent();
        if ($content == '') {
            return;
        }
        $url = 'http://images.guff.com' . $content;
        if ($this->output->isVerbose()) {
            $this->output->writeln('<comment>Contents: ' . $url . '</comment>');
        }
        $valid = false;

        $filename_from_url = parse_url($url);
        $ext = pathinfo(
            $filename_from_url['path'],
            PATHINFO_EXTENSION
        );
        $basename = pathinfo(
            $filename_from_url['path'],
            PATHINFO_FILENAME
        );
        if ($ext == '') {
            $ext = 'jpg';
        }
        $filename = 'media/' . $post->getId() . '/' . $basename . '.' . $ext;
        if ($this->s3->doesObjectExist($this->bucket, $filename)) {
            $this->output->writeln('<comment>Content Exists: ' . $filename . '</comment>');
            $valid = true;
        } else {
            try {
                $response = $this->client->get($url, ['stream' => true]);
                $file = $response->getBody()->getContents();
                $valid = $this->uploadImage($filename, $file, $ext);
            } catch (\Exception $e) {
                $this->output->writeln('<error>Post:' . $post->getId() . ' Thumbnail: ' . $url . ' not found</error>');
            }
        }
        if ($valid) {
            $url = '/' . $filename;
            //Do we have an entity for this media?
            $image = $this->em->getRepository('EmpireBundle:ImageMedia')->findOneBy(['url' => $url]);
            if (!$image) {
                $image = new ImageMedia();
                $image->setUrl($url);
                $image->setAuthor($post->getAuthor());
                $image->setPost($post);
                $image->setSite($post->getSite());
                $image->setStatus(1);
                $this->em->persist($image);
            }
            $post->setLeadContent($image->getUrl());
            $this->em->persist($post);
            $this->em->flush();

            if ($this->output->isVerbose()) {
                $this->output->writeln('<comment>Thumbnail: ' . $url . '</comment>');
            }
        }
    }

    /**
     *
     * @param type $post
     * @return type
     */
    private function manageCoverImage($post)
    {
        $cover = $post->getCoverImage();
        if ($cover == '') {
            return;
        }
        $url = 'http://images.guff.com/' . $cover;
        if ($this->output->isVerbose()) {
            $this->output->writeln('<comment>Cover: ' . $url . '</comment>');
        }
        try {
            $filename_from_url = parse_url($url);
            $ext = pathinfo(
                $filename_from_url['path'],
                PATHINFO_EXTENSION
            );
            if ($ext == '') {
                $ext = 'jpg';
            }
            $filename = 'media/' . $post->getId() . '/Cover.' . $ext;
            if ($this->s3->doesObjectExist($this->bucket, $filename)) {
                $this->output->writeln('<comment>Cover Exists: ' . $filename . '</comment>');
                $valid = true;
            } else {
                $response = $this->client->get($url, ['stream' => true]);
                $file = $response->getBody()->getContents();
                $valid = $this->uploadImage($filename, $file, $ext);
            }
            if ($valid) {
                $url = '/' . $filename;
                //Do we have an entity for this media?
                $image = $this->em->getRepository('EmpireBundle:ImageMedia')->findOneBy(['url' => $url]);
                if (!$image) {
                    $image = new ImageMedia();
                    $image->setUrl($url);
                    $image->setAuthor($post->getAuthor());
                    $image->setPost($post);
                    $image->setSite($post->getSite());
                    $image->setStatus(1);
                    $this->em->persist($image);
                }
                $post->setCoverMedia($image);
                $this->em->persist($post);
                $this->em->flush();

                if ($this->output->isVerbose()) {
                    $this->output->writeln('<comment>Thumbnail: ' . $url . '</comment>');
                }
            }
        } catch (\Exception $e) {
            $this->output->writeln('<error>Post:' . $post->getId() . ' Thumbnail: ' . $url . ' not found</error>');
        }
    }

    /**
     *
     * @param type $post
     * @return type
     */
    private function manageLeadOGImage($post)
    {
        $lead = $post->getLeadOGThumb();
        if ($this->output->isVerbose()) {
            $this->output->writeln('<comment>Lead OG: ' . $lead . '</comment>');
        }
        try {
            $filename_from_url = parse_url($lead);
            $ext = pathinfo(
                $filename_from_url['path'],
                PATHINFO_EXTENSION
            );
            $basename = pathinfo(
                $filename_from_url['path'],
                PATHINFO_FILENAME
            );
            if ($ext == '') {
                $ext = 'jpg';
            }
            $filename = 'media/' . $post->getId() . '/facebook/' . $basename . '.' . $ext;
            /*if ($this->s3->doesObjectExist($this->bucket, $filename)) {
                $this->output->writeln('<comment>Lead Exists: ' . $filename . '</comment>');
                $valid = true;
            } else {*/
                $response = $this->client->get($lead, ['stream' => true]);
            $file = $response->getBody()->getContents();
            $valid = $this->uploadImage($filename, $file, $ext);
            //}
            if ($valid) {
                $url = '/' . $filename;
                //Do we have an entity for this media?
                $image = $this->em->getRepository('EmpireBundle:ImageMedia')->findOneBy(['url' => $url]);
                if (!$image) {
                    $image = new ImageMedia();
                    $image->setUrl($url);
                    $image->setAuthor($post->getAuthor());
                    $image->setPost($post);
                    $image->setSite($post->getSite());
                    $image->setStatus(1);
                    $this->em->persist($image);
                }
                $post->setLeadOGMedia($image);
                $this->em->persist($post);
                $this->em->flush();

                if ($this->output->isVerbose()) {
                    $this->output->writeln('<comment>Thumbnail: ' . $url . '</comment>');
                }
            }
        } catch (\Exception $e) {
            $this->output->writeln('<error>Post:' . $post->getId() . ' Thumbnail: ' . $url . ' not found</error>');
            $this->output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

    /**
     *
     * @param type $post
     */
    private function manageFacebookThumbs($post)
    {
        $index = '';
        foreach ($post->getOgImages() as $key => $ogImage) {
            if ($ogImage == 0) {
                continue;
            }
            $url = 'http://images.guff.com/gallery/facebookedit' . $index . '/' . $post->getId() . '-' . $ogImage;
            try {
                $filename_from_url = parse_url($url);
                $ext = pathinfo(
                    $filename_from_url['path'],
                    PATHINFO_EXTENSION
                );
                if ($ext == '') {
                    $ext = 'jpg';
                }
                $filename = 'media/' . $post->getId() . '/' . $key . '.' . $ext;
                if ($this->s3->doesObjectExist($this->bucket, $filename)) {
                    $this->output->writeln('<comment>FB Thumb Exists: ' . $filename . '</comment>');
                    $valid = true;
                } else {
                    $response = $this->client->get($url, ['stream' => true]);
                    $file = $response->getBody()->getContents();
                    $valid = $this->uploadImage($filename, $file, $ext);
                }
                if ($valid) {
                    $url = '/' . $filename;
                    //Do we have an entity for this media?
                    $image = $this->em->getRepository('EmpireBundle:ImageMedia')->findOneBy(['url' => $url]);
                    if (!$image) {
                        $image = new ImageMedia();
                        $image->setUrl($url);
                        $image->setAuthor($post->getAuthor());
                        $image->setPost($post);
                        $image->setSite($post->getSite());
                        $image->setStatus(1);
                        $this->em->persist($image);
                    }
                    $setter = 'set' . $key;
                    $post->$setter($image);
                    $this->em->persist($post);
                    $this->em->flush();

                    if ($this->output->isVerbose()) {
                        $this->output->writeln('<comment>Thumbnail: ' . $url . '</comment>');
                    }
                }
            } catch (\Exception $e) {
                $this->output->writeln('<error>Post:' . $post->getId() . ' Thumbnail: ' . $url . ' not found</error>');
            }

            // index starts as empty string

            if ($index == '') {
                $index = 1;
            }
            $index++;
        }
    }

    /**
     *
     * @param type $post
     */
    private function manageYahooThumbs($post)
    {
        $index = '';
        foreach ($post->getYhImages() as $key => $yhImage) {
            if ($yhImage == 0) {
                continue;
            }
            $url = 'http://images.guff.com/gallery/yahoothumb' . $index . '/' . $post->getId() . '-' . $yhImage;
            try {
                $filename_from_url = parse_url($url);
                $ext = pathinfo(
                    $filename_from_url['path'],
                    PATHINFO_EXTENSION
                );
                if ($ext == '') {
                    $ext = 'jpg';
                }
                $filename = 'media/' . $post->getId() . '/' . $key . '.' . $ext;
                if ($this->s3->doesObjectExist($this->bucket, $filename)) {
                    $this->output->writeln('<comment>FB Thumb Exists: ' . $filename . '</comment>');
                    $valid = true;
                } else {
                    $response = $this->client->get($url, ['stream' => true]);
                    $file = $response->getBody()->getContents();
                    $valid = $this->uploadImage($filename, $file, $ext);
                }
                if ($valid) {
                    $url = '/' . $filename;
                    //Do we have an entity for this media?
                    $image = $this->em->getRepository('EmpireBundle:ImageMedia')->findOneBy(['url' => $url]);
                    if (!$image) {
                        $image = new ImageMedia();
                        $image->setUrl($url);
                        $image->setAuthor($post->getAuthor());
                        $image->setPost($post);
                        $image->setSite($post->getSite());
                        $image->setStatus(1);
                        $this->em->persist($image);
                    }
                    $setter = 'set' . $key;
                    $post->$setter($image);
                    $this->em->persist($post);
                    $this->em->flush();

                    if ($this->output->isVerbose()) {
                        $this->output->writeln('<comment>Thumbnail: ' . $url . '</comment>');
                    }
                }
            } catch (\ Exception $e) {
                $this->output->writeln('<error>Post:' . $post->getId() . ' Thumbnail: ' . $url . ' not found</error>');
            }

            // index starts as empty string

            if ($index == '') {
                $index = 1;
            }
            $index++;
        }
    }

    /**
     *
     * @param type $filename
     * @param type $file
     * @param type $ext
     * @return boolean
     */
    private function uploadImage($filename, $file, $ext)
    {
        if ($this->output->isVerbose()) {
            $this->output->writeln('<comment>Upload Image: ' . $filename . '</comment>');
        }

        try {
            /* $exist = $this->s3->doesObjectExist($this->bucket, $filename);
              if ($exist) {
              return true;
              } */
            $this->s3->putObject(
                [
                'Bucket' => $this->bucket,
                'Key' => $filename,
                'Body' => $file,
                'ACL' => 'public-read',
                'ContentType' => MimeType::get_mimetype($ext),
                ]
            );
            return true;
        } catch (Aws\Exception\S3Exception $e) {
            $this->view['valid'] = "There was an error uploading the file";
        }
        return false;
    }

    /**
     *
     * @param type $filename
     * @param type $file
     * @param type $ext
     * @return boolean
     */
    private function copyImage($filename, $source, $ext)
    {
        if ($this->output->isVerbose()) {
            $this->output->writeln('<comment>Copy Image: ' . $filename . '</comment>');
        }

        try {
            /* $exist = $this->s3->doesObjectExist($this->bucket, $filename);
              if ($exist) {
              return true;
              } */
            $this->s3->putObject(
                [
                'Bucket' => $this->bucket,
                'Key' => $filename,
                'CopySource' => $source,
                'ACL' => 'public-read',
                'ContentType' => MimeType::get_mimetype($ext),
                ]
            );
            return true;
        } catch (Aws\Exception\S3Exception $e) {
            $this->view['valid'] = "There was an error copying the file";
        }
        return false;
    }
}
