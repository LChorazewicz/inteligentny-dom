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
                    case 5:{//rolled-up
                        let icon = $('#device-' + deviceId + " .state-icon");
                        icon.removeClass('icon-rolled-down').addClass('icon-rolled-up');
                        break;
                    }
                    case 6:{//rolled-down
                        let icon = $('#device-' + deviceId + " .state-icon");
                        icon.removeClass('icon-rolled-up').addClass('icon-rolled-down');
                        break;
                    }
                }

            }
        });
    }
    correctState(deviceId, option){
        $.ajax({
            url: Endpoint.getCorrectStateEndpoint(),
            method: "POST",
            data: {
                deviceId: deviceId,
                rotation: option
            }
        }).done(function(response, responseText) {
            console.log(response);
            console.log(responseText);
        });
    }

    static toggleNavigation(){
        let menu = $(".menu");
        menu.toggleClass("open");
    }
}