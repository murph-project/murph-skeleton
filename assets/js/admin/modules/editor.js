const initEditor = function(element) {
    tinymce.init({
        selector: element,
        base_url: '/vendor/tinymce/',
        cache_suffix: '?v=4.1.6',
        language: 'fr_FR',
        plugins: 'print preview importcss searchreplace visualblocks visualchars fullscreen template table charmap hr pagebreak nonbreaking toc insertdatetime advlist lists wordcount textpattern noneditable help charmap quickbars link image code autoresize',
        menubar: 'file edit view insert format tools table tc help',
        toolbar: 'undo redo | bold italic underline strikethrough | link image | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap | fullscreen  preview',
        importcss_append: true,
        image_caption: true,
        quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
        noneditable_noneditable_class: "mceNonEditable",
        toolbar_drawer: 'sliding',
        spellchecker_dialog: true,
        tinycomments_mode: 'embedded',
        contextmenu: "link image imagetools table configurepermanentpen",
        convert_urls: false,
    });
}

module.exports = function() {
    if (typeof tinymce === 'undefined') {
        return;
    }

    const doInitEditor = function() {
        initEditor('*[data-tinymce]');
    }
    const observer = new MutationObserver(doInitEditor);
    const config = {attributes: false, childList: true, subtree: true};

    doInitEditor();
    observer.observe(document.querySelector('body'), config);
};