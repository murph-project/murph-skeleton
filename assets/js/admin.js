/*const imagesContext = require.context(
    '../images',
    true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/
);

imagesContext.keys().forEach(imagesContext);*/

import '../css/admin.scss';

require('../../node_modules/bootstrap/dist/js/bootstrap.min.js');
// require('./addons/table-selectable.js')();
require('./addons/table-fixed.js')();
// require('./addons/document-selector.js')();
require('./addons/form-confirm.js')();
require('./addons/form.js')();
require('./addons/dbclick.js')();
require('./addons/toast.js')();
require('./addons/modal.js')();
require('./addons/push-state.js')();
require('./addons/password.js')();
require('./addons/tooltip.js')();
require('./addons/editor.js')();
require('./addons/panel.js')();
require('./addons/choices.js')();
require('./addons/checkbox-checker.js')();
require('./addons/rest-choices.js')();
require('./addons/form-collection.js')();
require('./addons/datepicker.js')();
