class Application {
    changeState(deviceId){
        $.ajax({
            url: Endpoint.getChangeStateEndpoint(),
            method: "POST",
            data: {
                deviceId: deviceId
            }
        }).done(function(response, responseText) {
            console.log(response);
            console.log(responseText);
            if(responseText === 'success'){
                switch (response.state) {
                    case 1:{
                        let icon = $('#device-' + deviceId + " .state-icon");
                        icon.removeClass('icon-locked').addClass('icon-unlocked');
                        break;
                    }
                    case 2:{
                        let icon = $('#device-' + deviceId + " .state-icon");
                        icon.removeClass('icon-unlocked').addClass('icon-locked');
                        break;
                    }
                }

            }
        });
    }

    static toggleNavigation(){
        let menu = $(".menu");
        menu.toggleClass("open");
    }
}