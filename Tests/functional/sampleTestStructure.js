casper.test.begin('Sample test', 0, function() {
    //define modules, start with oxidModule if you want to access config
    var oxidModule = require('../modules/oxid');

    //starts the casper and also checks if website is accessible
    oxidModule.start();

    //other test logic and assertions go here

    //ends the casper test
    oxidModule.end();
});

