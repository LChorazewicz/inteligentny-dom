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
                    case 1:{//locked
                        let icon = $('#device-' + deviceId + " .state-icon");
                        icon.removeClass('icon-unlocked').addClass('icon-locked');
                        break;
                    }
                    case 2:{//unlocked
                        let icon = $('#device-' + deviceId + " .state-icon");
                        icon.removeClass('icon-locked').addClass('icon-unlocked');
                        break;
                    }
                    case 3:{//turned-on
                        let icon = $('#device-' + deviceId + " .state-icon");
                        icon.removeClass('icon-turned-off').addClass('icon-turned-on');
                        break;
                    }
                    case 4:{//turned-off
                        let icon = $('#device-' + deviceId + " .state-icon");
                        icon.removeClass('icon-turned-on').addClass('icon-turned-off');
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