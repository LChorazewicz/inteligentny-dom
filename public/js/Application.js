class Application {
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
        });
    }

    static toggleNavigation(){
        let menu = $(".menu");
        menu.toggleClass("open");
    }

    openModalForDevice(modalId, deviceId){
        let context = this;
        this.getDeviceInfo(deviceId, function (response) {
            context._openModalForDevice(modalId + "-" + deviceId, response);
        });
    }

    getDeviceInfo(deviceId, callback){
        $.ajax({
            url: Endpoint.getDeviceInfoEndpoint(),
            method: "GET",
            data: {
                device_id: deviceId,
            }
        }).done(callback);
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
        console.log('wszedłem', data);
        switch (data.deviceType) {
            case 4:{
                let slider = $('#' + modalId + ' #move-' + data.deviceId);
                // slider.attr('value', data.currentTurn !== null ? data.currentTurn : 0);
                slider.val(data.currentTurn !== null ? parseInt(data.currentTurn) : 0);
                slider.attr('max', data.turns);

                let changeState = $('#' + modalId + ' #changeState');
                changeState.text(data.state === 5 ? "Roll down" : "Roll up");
            }
        }
        this.initTiles(data.deviceId)
    }

    initTiles(deviceId, data){
        this.getDeviceInfo(deviceId, function (data) {
            switch (data.state) {
                case 1:{//locked
                    let icon = $('#device-' + data.deviceId + " .state-icon");
                    icon.removeClass('icon-unlocked').addClass('icon-locked');
                    break;
                }
                case 2:{//unlocked
                    let icon = $('#device-' + data.deviceId + " .state-icon");
                    icon.removeClass('icon-locked').addClass('icon-unlocked');
                    break;
                }
                case 3:{//turned-on
                    let icon = $('#device-' + data.deviceId + " .state-icon");
                    icon.removeClass('icon-turned-off').addClass('icon-turned-on');
                    break;
                }
                case 4:{//turned-off
                    let icon = $('#device-' + data.deviceId + " .state-icon");
                    icon.removeClass('icon-turned-on').addClass('icon-turned-off');
                    break;
                }
                case 5:{//rolled-up
                    let icon = $('#device-' + data.deviceId + " .state-icon");
                    icon.removeClass('icon-rolled-down').addClass('icon-rolled-up');
                    break;
                }
                case 6:{//rolled-down
                    let icon = $('#device-' + data.deviceId + " .state-icon");
                    icon.removeClass('icon-rolled-up').addClass('icon-rolled-down');
                    break;
                }
            }

            switch (data.state) {
                case 5://rolled-up
                case 6:{//rolled-down
                    let parent = $('#device-' + data.deviceId);
                    let icon = parent.find('i.state-icon');

                    icon.remove();

                    let newIcon = document.createElement("i");
                    newIcon.className += 'icon';
                    newIcon.className += ' state-icon';

                    if(data.state === 5){
                        newIcon.className += ' icon-rolled-up';
                    }else if(data.state === 6){
                        newIcon.className += ' icon-rolled-down';
                    }

                    let classToBeAdded = '';
                    if(data.openDegree >= 1 && data.openDegree <= 10){
                        classToBeAdded = 'icon-rolled-up-10';
                    }else if(data.openDegree >= 11 && data.openDegree <= 20){
                        classToBeAdded = 'icon-rolled-up-20';
                    }else if(data.openDegree >= 21 && data.openDegree <= 30){
                        classToBeAdded = 'icon-rolled-up-30';
                    }else if(data.openDegree >= 31 && data.openDegree <= 40){
                        classToBeAdded = 'icon-rolled-up-40';
                    }else if(data.openDegree >= 41 && data.openDegree <= 50){
                        classToBeAdded = 'icon-rolled-up-50';
                    }else if(data.openDegree >= 51 && data.openDegree <= 60){
                        classToBeAdded = 'icon-rolled-up-60';
                    }else if(data.openDegree >= 61 && data.openDegree <= 70){
                        classToBeAdded = 'icon-rolled-up-70';
                    }else if(data.openDegree >= 71 && data.openDegree <= 80){
                        classToBeAdded = 'icon-rolled-up-80';
                    }else if(data.openDegree >= 81 && data.openDegree <= 90){
                        classToBeAdded = 'icon-rolled-up-90';
                    }else if(data.openDegree >= 91 && data.openDegree <= 100){
                        classToBeAdded = 'icon-rolled-up-100';
                    }

                    newIcon.className += ' ' + classToBeAdded;
                    parent.append(newIcon);
                    break;
                }
            }
        });
    }
    moveBlinds(deviceId, step){
        let context = this;
        $.ajax({
            url: Endpoint.getMoveBlindsEndpoint(),
            method: "POST",
            data: {
                deviceId: deviceId,
                step: step
            }
        }).done(function(response) {
            console.log("step: " + step, response);
            context.updateModal('modal-device-' + deviceId, response);
        });
    }

    changeState(deviceId){
        let context = this;
        $.ajax({
            url: Endpoint.getChangeStateEndpoint(),
            method: "POST",
            data: {
                deviceId: deviceId
            }
        }).done(function(response) {
            console.log(response);
            context.updateModal('modal-device-' + deviceId, response)
        });
    }

    static closeModal(modalId, deviceId){
        $("#" + modalId).hide();
    }
}