class Application {
    changeState(deviceId){
        let context = this;
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
                context.updateModal('modal-device-' + response.deviceId, response)
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

    openModalForDevice(modalId, deviceId){
        let context = this;
        $.ajax({
            url: Endpoint.getDeviceInfoEndpoint(),
            method: "GET",
            data: {
                device_id: deviceId,
            }
        }).done(function(response) {
            context._openModalForDevice(modalId + "-" + deviceId, response);
        });
    }

    _openModalForDevice(modalId, data){
        console.log(data);
        this.updateModal(modalId, data);
        $("#" + modalId).show();
    }

    /**
     * @param modalId
     * @param data
     * @desc modalId = modal-device-10, data = device dto
     */
    updateModal(modalId, data){
        switch (data.deviceType) {
            case 4:{
                let slider = $('#' + modalId + ' #move');
                slider.attr('value', data.openDegree !== null ? data.openDegree : 0);

                let changeState = $('#' + modalId + ' #changeState');
                changeState.text(data.state === 5 ? "Roll down" : "Roll up");

                console.log('#' + modalId + ' #move', '#' + modalId + ' #changeState');
            }
        }
    }

    moveBlinds(deviceId, percent){
        let context = this;
        $.ajax({
            url: Endpoint.getMoveBlindsEndpoint(),
            method: "POST",
            data: {
                deviceId: deviceId,
                percent: percent
            }
        }).done(function(response) {
            context.updateModal('modal-device-' + deviceId, response);
        });
    }

    static closeModal(modalId, deviceId){
        $("#" + modalId).hide();
    }
}