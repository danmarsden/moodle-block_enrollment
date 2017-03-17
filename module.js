M.block_enrollment = {
    init : function(Y){
        this.xhr = null;
        if (window.XMLHttpRequest || window.ActiveXObject) {
            if (window.ActiveXObject) {
                try {
                    this.xhr = new ActiveXObject("Msxml2.XMLHTTP");
                } catch(e) {
                    this.xhr = new ActiveXObject("Microsoft.XMLHTTP");
                }
            } else {
                this.xhr = new XMLHttpRequest(); 
            }
        } else {
            alert("Your browser doesn't support XMLHTTPRequest...");
            return null;
        }
        
        var selectElmt = document.getElementById('insc_users');
        this.refreshCourses(selectElmt.options[selectElmt.selectedIndex]);
    },
    refreshCourses : function(elem){
        var user = elem.value;
        var that = this;
        this.xhr.onreadystatechange = function() {
            if (that.xhr.readyState == 4 && (that.xhr.status == 200 || that.xhr.status == 0)) {
                var json = {};
                json = that.xhr.responseText;
                var objects = JSON.parse(json);
                document.form.courses.options.length = 0;
                for(var i=0;i<objects.length;i++){
                    document.form.courses.options[document.form.courses.options.length] = new Option(objects[i].shortname, objects[i].id);
                }
            }
        };
        this.xhr.open("POST", "refresh.php", true);
        this.xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        this.xhr.send("user="+user);
    }
};

