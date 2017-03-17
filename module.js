M.block_enrollment = {
    init: function(Y) {
        var that = this;
        $('#insc_users').on('change', function() {
            that.refreshCourses($(this).val());
        });
        $('#datestart').datepicker();
        $('#dateend').datepicker();

        this.refreshCourses($('#insc_users').val());
    },
    refreshCourses: function(user) {
        $.post("refresh.php", {user: user}, function(data) {
            $('#courses').empty();
            for (var course in data) {
                $('#courses').append($("<option></option>").attr("value", data[course].id).text(data[course].shortname));
            }
        }, 'json'
                );
    }
};

