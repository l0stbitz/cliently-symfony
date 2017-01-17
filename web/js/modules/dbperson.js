var Dbperson = (function () {
    var scope = {};

    var dbperson = null;

    scope.load = function () {
        return $.get('/api/v1/ui/sets/dbperson', function (data) {
            dbperson = data;
        });
    };
    scope.getDbperson = function () {
        return dbperson;
    };

    scope.getDbpersonNamesByValues = function (field, values) {
        var names = null;
        if (values && values.length > 0) {
            names = [];
            for (var i in values) {
                names.push(getDbpersonNameByValue(field, values[i]));
            }
        }
        return names;
    };

    function getDbpersonNameByValue(field, value) {
        return __getDbpersonNameByValue(dbperson[field], value);
    }
    function __getDbpersonNameByValue(infos, value) {
        var name = null;
        $.each(infos, function (i, info) {
            if (info[0] == value) {
                name = info[1];
                return false;
            } else if (typeof info[2] != 'undefined') {
                name = __getDbpersonNameByValue(info[2], value);
                if (name) {
                    return false;
                }
            }
        });

        return name;
    }

    return scope;
})();