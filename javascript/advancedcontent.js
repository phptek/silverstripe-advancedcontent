/**
 * Created by russellm on 17/04/16.
 */

(function($) {

    $.entwine('ss', function ($) {
        
        $('.ss-gridfield .ss-gridfield-item .gridfield-button-showblockattributes').entwine({

            /**
             * Simply prevents the GridField loading an edit screen and shows a block's attribute UI control overlay
             * within the GridField itself instead.
             *
             * @param HTMLDOmObject e
             * @returns {boolean}
             */
            onclick: function (e) {
                e.preventDefault();
                e.stopPropagation();

                this.showAttributeOverlay();

                return false;
            },

            /**
             * Show an "overlay" containing all our attribute's UI controls for quick access to a block's grid-available feature(s).
             *
             * @returns void
             * @todo
             */
            showAttributeOverlay: function() {
                $('#advanced-content-attribute-controls').toggleClass('hide');
            }
        });

        $('.ss-gridfield .ss-gridfield-item #advanced-content-attribute-controls input').entwine({

            /**
             * Simply prevents the GridField loading an edit screen.
             *
             * @param HTMLDOmObject e
             * @returns {boolean}
             */
            onclick: function (e) {
                e.preventDefault();
                e.stopPropagation();
                
                return false;
            },
            
            onkeypress: function(e) {
                this.sendAttributeData();
            },

            /**
             * 
             */
            sendAttributeData: function() {
                var keyIsEnter = e.which === 13,
                    actionHref = this.parent().data('action-href');

                if(keyIsEnter) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    $.ajax({
                        headers: {
                            "X-Pjax": 'Partial'
                        },
                        type: 'POST',
                        url: actionHref,
                        data: {"foo": 'bar'},
                        success: function (data) {
                            console.log('#SUCESSS');
                            return false;
                        },
                        error: function (e) {
                            console.log('#ERROR');
                            return false;
                        }
                    });
                }
            }
        });
        
    });

})(jQuery);
