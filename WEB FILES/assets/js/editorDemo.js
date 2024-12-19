(function($) {
  'use strict';
  /*Quill editor*/
  if ($("#quillExample1").length) {
    var quill = new Quill('#quillExample1', {
      modules: {
        toolbar: [
          [{
            header: [1, 2, false]
          }],
          ['bold', 'italic', 'underline']
        ]
      },
      text: 'Test 1',
      theme: 'snow'
    });
  }

})(jQuery);