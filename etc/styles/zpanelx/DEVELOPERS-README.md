# ZPanel JavaScript API and CSS Usage #

Below I will try to outline some of the JavaScript and CSS functionality that I built into the new zPanel theme.  You can use any of this stuff in your own Modules and themes that you build, as long as all the proper files are loaded.

If you have any questions about this theme, you can ask on the forums [zPanel Forums](http://forums.zpanelcp.com/forum.php) or contact me (Jason Davis [http://www.codedevelopr.com](http://www.codedevelopr.com))

## Over-ride Module Icons ##
You can replace the module icons that come with a Module on the zPanel dashboard screen with your own icons.

To over-ride a Modules icon, simply create a folder and image file in this location...

    /etc/styles/zpanelx/images/MODULE-NAME/assets/icon.png

Replacing **MODULE-NAME** with the name of the module you are replacing.  The Module name has to be the exact same name that the module uses in the modules folder for this over-ride to work!

An example to over-ride the MySQL Database module's icon...

    /etc/styles/zpanelx/images/mysql_databases/assets/icon.png


## Console Logging ##
You can log to the Browsers JavaScript Console for debugging using this function below.

    zPanel.utils.log('my debug data');

As long as this Property `zPanel.utils.settings.debug = false;` is set to `TRUE` then the all data will print to console.  

Setting to false will turn it off allowing you to keep all your debugging code in place.

## JavaScript Loader Screen ##
![Loading screenshot](http://i.imgur.com/T9njoHA.png)

You can Programmatically show the loading screen and spinner with...

    zPanel.loader.showLoader()

and hide/kill it with...


    zPanel.loader.hideLoader()

To have the Loader screen show automatically when a `Button` is clicked.  You can do a Button HTML like this...

    <button class="button-loader btn btn-danger">Delete</button>

The  `button-loader` class is what triggers the Loader to show on a click event.  

> **Pro Tip:** Be careful not to use it on Buttons that load a Pop-up window as you will leave the main screen with a never ending loading screen!


## Notice Div##
![Notice screenshot](http://i.imgur.com/9Yz7MAN.png)

You can Programmatically insert a **Notice Div** into the DOM with the function below.

You can set if the Notice will have a Close X Button or not, the Notice CSS Class type, if the Notice should automatically close after a certain amount of seconds or remain on the page permanently .

**Notice Example Usage:**

    zPanel.notice.show({
        type: "success",
        selector: "#alert-area",
        closeButton: true,
        autoClose: true,
        closeTime: 6000,
        message: "<strong>Warnig:</strong> Show my custom notice message here"
    });

**Notice Property Settings:**

    type: (STRING) Alert type "success", "error", or "info"
    selector: (STRING) Specify the Div ID to Append the Notice to.
    closeButton: (BOOLEAN) Show Close X Button true or false
    autoClose: (BOOLEAN) Automatically close after X ammount of seconds- true or false
    closeTime: (INT) Time to before closing if autoClose is True (6000)
    message (STRING): The Notice Message


## Dialog Window ##
![Dialog screenshot](http://i.imgur.com/ONrP9nh.png)

The Dialog has many settings as well.  You can set the title, message content, div width, if the cancel or ok buttons should be present or not and also the text and CSS class for those buttons.

Lastly the most flexible part is you can even set your own custom callback functions to run when the cancel or ok button is clicked!


**Dialog Example Usage:**

    zPanel.dialog.confirm({
        title: 'WARNING',
        message: 'Warning message content here',
        width: 300,
        cancelButton: {
            text: 'Cancel',
            show: true,
            class: 'btn-default'
        },
        okButton: {
            text: 'Confirm',
            show: true,
            class: 'btn-primary'
        },
        cancelCallback: function() { },
        okCallback: function() { },
    });

**Dialog Property Settings:**

    title: (STRING) The header text
    message (STRING): The Dialog Message Content
    width: (INT) The width of the Dialog Div in pixels

    cancelButton.show: (BOOLEAN) Show Cancel button true/false
    cancelButton.text: (STRING) Cancel button text
    cancelButton.class: (STRING) CSS Class to assign to the button.

    okButton.show: (BOOLEAN) Show Ok button true/false
    okButton.text: (STRING) Ok button text
    okButton.class: (STRING) CSS Class to assign to the button.

    cancelCallback: (FUNCTION) A callbacck function to run when the Cancel button is clicked
    okCallback: (FUNCTION) A callbacck function to run when the Ok button is clicked

## Some CSS ##
This new theme utilizes the new Bootstrap 3 which is an amazing library.  At the time we built this theme, BS3 was still under development and not 100% complete.

For that reason you should **NOT** download and use the latest Bootstrap 3 as it will break some things and simply make some things not look correct.

Now that is out of the way, please realize, you can use 99% of the Bootstrap 3 features in your own Module or theme.  Below I outline some of the basic stuff we use.  To really see everything that's available you need to download and compile the Bootstrap 3 documentation files from there Git repo [twitter.github.com/bootstrap/](twitter.github.com/bootstrap/)

### Tables ###
You can style tables really nicely by applying the `table` class to a table.

You can then further enhance it by making alternating row striped by adding the `table-striped` class as well.
 
    <table class="table table-striped" border="0" width="100%">

### Buttons ###

There are several properties for the nice Bootstrap buttons.

The examples below show Button HTML 

> **Pro Tip:** Use the button classes on an `<a>`, `<button>`, or `<input>` elements.




![button screenshot](http://i.imgur.com/eZvOwHs.png)

    <!-- Standard gray button with gradient -->
    <button type="button" class="btn btn-default">Default</button>
    
    <!-- Provides extra visual weight and identifies the primary action in a set of buttons -->
    <button type="button" class="btn btn-primary">Primary</button>
    
    <!-- Indicates a successful or positive action -->
    <button type="button" class="btn btn-success">Success</button>
    
    <!-- Contextual button for informational alert messages -->
    <button type="button" class="btn btn-info">Info</button>
    
    <!-- Indicates caution should be taken with this action -->
    <button type="button" class="btn btn-warning">Warning</button>
    
    <!-- Indicates a dangerous or potentially negative action -->
    <button type="button" class="btn btn-danger">Danger</button>


![button sizes](http://i.imgur.com/54bSyxR.png)

    <p>
      <button type="button" class="btn btn-primary btn-large">Large button</button>
      <button type="button" class="btn btn-default btn-large">Large button</button>
    </p>
    <p>
      <button type="button" class="btn btn-primary">Default button</button>
      <button type="button" class="btn btn-default">Default button</button>
    </p>
    <p>
      <button type="button" class="btn btn-primary btn-small">Small button</button>
      <button type="button" class="btn btn-default btn-small">Small button</button>
    </p>

### Panels ###
Panels are one of my favorite new things in Bootstrap 3 and they are used a lot in ZPanel!

You can use them as well....

![Panel screenshot](http://i.imgur.com/DIT1cpe.png)

    <div class="panel">
      <div class="panel-heading">Panel heading</div>
      Panel content
    </div>

![panels screenshot](http://i.imgur.com/ijLyLdD.png)

    <div class="panel panel-primary">...</div>
    <div class="panel panel-success">...</div>
    <div class="panel panel-warning">...</div>
    <div class="panel panel-danger">...</div>
    <div class="panel panel-info">...</div>