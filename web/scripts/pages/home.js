$(document).ready(function () {
  // Don't close the dropdown then click on the labels.
  $('.dropdown-menu input, .dropdown-menu label').click(function (e) {
    e.stopPropagation();
  });

  // Initliaze Syntax Highlighter
  initializeSyntaxHighlighter();

  // Submitting crawling form.
  $("form#crawling").submit(function (e) {
    // Get Form Options.
    // get all the inputs into an array.
    var options = {};
    options['elements'] = [];
    var elementCounter = 0;

    // Load Recurrsive option into options object.
    options['recurrsive'] = ($(this).find("input[name='recurrsive']:checked").length > 0) ? true : false;

    // Load all checked elements options into the options object.
    $(this).find("input[name='elements']:checked").each(function () {
      options['elements'][elementCounter] = this.value;
      elementCounter++
    })

    // If user didn't select enough elements to display (Images, CSS, JS..etc)
    if (options['elements'].length == 0) {
      alert("Ooops. you didn't pick enough elements to display!");
      return false;
    }

    // Enable loading mode.
    enableLoading();

    // If recurssive disabled, Retrieve page elements directrly.
    if (!options['recurrsive']) {
      // Update loading text.
      updateLoadingText("Fetching stuff from the page..");
      // If user didn't select enough elements to display (Images, CSS, JS..etc)
      if (options['elements'].length == 0) {
        alert("Ooops. you didn't pick enough elements to display!");
        disableLoading();
      }
      // Prepare Ajax Parameters
      var urls = $("input[name='url']").val();
      var elements = options['elements'].join(",");
      // Make Ajax Request.
      $.post( "ajax/elements", {urls: urls, elements: elements}, function (data) {
        // Replace code highlighter with retrieved JSON and disable loading
        $(".code-wrapper code").text(data);
        initializeSyntaxHighlighter();
        $(".code-wrapper").removeClass("hide");
        disableLoading();
      });

    }

    e.preventDefault();
  });

  /**
   * Enable disable mode for the page.
   */
  function enableLoading() {
    // Disable all form inputs and buttons.
    $("form#crawling input, form#crawling button").attr("disabled", "true");
    // Show Loading icon.
    $(".loading-wrapper").removeClass("hide");
  }

  /**
   * Disable disable mode for the page.
   */
  function disableLoading() {
    // Enable all form inputs and buttons.
    $("form#crawling input, form#crawling button").removeAttr("disabled");
    // Hide Loading icon.
    $(".loading-wrapper").addClass("hide");
  }

  /**
   * Update loading text.
   */
   function updateLoadingText(text) {
      $('.loading-wrapper .loading-text').fadeOut(1000, function () {
        var div = $("<div class='loading-text'>" + text + "</div>").hide();
        $(this).replaceWith(div);
        $('.loading-wrapper .loading-text').fadeIn(1000);
      });
   }

   /**
    * Initialize Syntax Highlighter.
    */
   function initializeSyntaxHighlighter() {
     $('pre code').each(function (i, block) {
       hljs.highlightBlock(block);
     });
  }

});
