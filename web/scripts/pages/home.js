$(document).ready(function () {
  var numOfItemsRetrieved = 0;
  var retrievedUrlsData = [];
  var options = {};
  options['elements'] = [];

  // Don't close the dropdown then click on the labels.
  $('.dropdown-menu input, .dropdown-menu label').click(function (e) {
    e.stopPropagation();
  });

  // Submitting crawling form.
  $("form#crawling").submit(function (e) {
    // Get Form Options.
    // get all the inputs into an array.
    var elementCounter = 0;
    var linksToCrowl = [];
    // Hide code block
    disableCodeBlock();
    // Reset previous results.
    retrievedUrlsData = [];
    numOfItemsRetrieved = 0;
    options = {};
    options['elements'] = [];
    $('.loading-wrapper .loading-text').text('Loading');

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
        enableCodeBlock(data);
        disableLoading();
      });

    }

    // If recurrsive option enabled
    if (options['recurrsive']) {
      // Update loading text.
      updateLoadingText("Retrieve internal links..");
      // Make Ajax Request to Retrieve internal links.
      var url = $("input[name='url']").val();
      var links = [];
      $.post( "ajax/links", {url: url}, function (data) {
        linksToCrowl = JSON.parse(data);
        // In case of no links retrieved;
        if(linksToCrowl.length == 0) {
          disableLoading();
          alert("We didn't find any links for this website");
          return false;
        }
        // Founds links update text.
        updateLoadingText("Found " + linksToCrowl.length + " links to crawl in this page. Lets start the fun!");
        // Retrieve bulk of links
        retrieveLinks(linksToCrowl);
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
    * Enable Code Block.
    */
   function enableCodeBlock(text) {
     $(".code-wrapper code").text(text);
     $('.code-wrapper code').each(function(i, block) {
        hljs.highlightBlock(block);
      });
     $(".code-wrapper").removeClass("hide");
   }

   /**
    * Disable Code Block
    */
   function disableCodeBlock() {
     $(".code-wrapper code").text('');
     $(".code-wrapper").addClass("hide");
   }

   /**
    * Retrieve bulk of data
    */
   function retrieveLinks(linksToCrowl) {
     // Per request limit
     let perRequestLimit = 10;
     // Urls to pull
     let perRequestUrls = [];

     if(linksToCrowl.length > 0) {
       // Get patch of urls to request.
      $.each(linksToCrowl, function (index, value) {
        if (perRequestLimit == 0 && linksToCrowl.length > 10) {
          return false;
        }
        perRequestUrls.push(value);
        linksToCrowl.splice(index, 1);
        perRequestLimit--;
      });

      // Prepare variables for POST request.
      let urls = perRequestUrls.join(",");
      let elements = options['elements'].join(",");

      $.post( "ajax/elements", {urls: urls, elements: elements}, function (data) {
        data = JSON.parse(data);
        retrievedUrlsData = retrievedUrlsData.concat(data);
        updateLoadingText(linksToCrowl.length + " URLs remaining");
        retrieveLinks(linksToCrowl);
      });
     }
     else {
       if(retrievedUrlsData.length > 0) {
         // Replace code highlighter with retrieved JSON and disable loading
         enableCodeBlock(JSON.stringify(retrievedUrlsData, null, "\t"));
       }
       disableLoading();
     }
   }

});
