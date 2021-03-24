const $ = require('jquery');

let Pannel = () => {
    let panels = $('.panel');

    panels.each((i, p) => {
        let panel = $(p);
        let content = panel.find('.panel-content').first();
        let togglers = panel.find('.panel-toggler');

        togglers.each((k, t) => {
            let toggler = $(t);

            if (!toggler.is('.fa')) {
                return;
            }

            if (content.is('.active')) {
                toggler.removeClass('fa-arrow-down');
                toggler.addClass('fa-arrow-up');
            } else {
                toggler.removeClass('fa-arrow-up');
                toggler.addClass('fa-arrow-down');
            }
        })

        togglers.click(function(e) {
            e.stopPropagation();

            content.toggleClass('active');

            togglers.each((k, t) => {
                let toggler = $(t);

                if (!toggler.is('.fa')) {
                    return;
                }

                toggler
                    .toggleClass('fa-arrow-down')
                    .toggleClass('fa-arrow-up');
            })
        });
    });
}

module.exports = Pannel;
