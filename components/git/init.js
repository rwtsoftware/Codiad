/*
 *  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
 *  as-is and without warranty under the MIT License. See
 *  [root]/license.txt for more. This information must remain intact.
 */

(function(global, $) {

    var codiad = global.codiad;

    $(function() {
        codiad.git.init();
    });

    //////////////////////////////////////////////////////////////////
    //
    // Git Component for Codiad
    // ---------------------------------
    // Track and manage project's git status
    //
    //////////////////////////////////////////////////////////////////

    codiad.git = {

        controller: 'components/git/controller.php',

        submit: function() {

            var _this = this;

            $.get(codiad.git.controller + '?action=submit', function(data) {
                var obj = JSON.parse(data);
                console.log(obj.data.submit);
                codiad.git.stash();
            });

        },

        diff: function() {

            var _this = this;

            $('#diff-window').text("");
            $.get(codiad.git.controller + '?action=diff', function(data) {
                var obj = JSON.parse(data);
                if (obj.data.diff != null) {
                    $('#diff-window').text(obj.data.diff);
                } else {
                    $('#diff-window').text("No Changes Saved");
                }
            });

        },

        stash: function() {

            var _this = this;
            var fn = function(sessions, path) {
                return function(data) {
                    var openResponse = codiad.jsend.parse(data);
                    if (openResponse != 'error') {
                        //TODO: remove green hihglighting of tabs
                        sessions[path].getDocument().setValue(openResponse.content);
                    }
                }
            }

            $.get(codiad.git.controller + '?action=stash', function(data) {
                var obj = JSON.parse(data);
                console.log(obj.data.stash);
                codiad.git.diff();

                for (var path in codiad.active.sessions) {
                    if (codiad.active.sessions[path].type === 'ace') {
                        $.get(codiad.filemanager.controller + '?action=open&path=' + encodeURIComponent(path), fn(codiad.active.sessions, path));
		    }
                }
            });
        },

        init: function() {


        },

    };

})(this, jQuery);
