$(function() {
    $('.evidence')
        .on('click', '.play-video', function() {
            $(this).hide()
                .closest('.evidence').addClass('playing')
                    .find('video').prop('controls', true).get(0).play();
            return false;
        })
        .on('click', '.play-audio', function() {
            $(this).hide()
                .closest('.evidence')
                    .find('audio').prop('controls', true).get(0).play();
            return false;
        })
        .on('click', '.fullscreen', function() {
           var el = $(this).closest('.actions-overlay').siblings('.item').get(0),
               rfs = el.requestFullscreen
                   || el.webkitRequestFullScreen
                   || el.mozRequestFullScreen
                   || el.msRequestFullscreen;
           rfs.call(el);
           return false;
        })
        .on('click', '.metadata', function() {
            var evidence = $(this).closest('.evidence');
            if (evidence.hasClass('metadata-shown')) {
                evidence.next('.evidence.metadata').slideToggle(function() { evidence.toggleClass('metadata-shown'); });
            } else {
                evidence.toggleClass('metadata-shown').next('.evidence.metadata').slideToggle();
            }
            
            return false;
        });
    ;
    
    $('.dotme').dotdotdot({
        after: 'a.read-more',
        watch: 'window'
    }).on('click', 'a.read-more', function() {
        var $this = $(this), $dot = $this.closest('.dotme');
        if ($dot.hasClass('is-truncated')) {
            $dot.trigger('destroy.dot').addClass('dot-reading-more');
        } else {
            $this.hide();
        }
        return false;
    }).on('click', 'a.read-less', function() {
        var $this = $(this), $dot = $this.closest('.dotme');
        $dot.removeClass('dot-reading-more').dotdotdot({
            after: 'a.read-more'
        });
        return false;
    });
});