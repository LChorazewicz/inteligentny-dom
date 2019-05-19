class Endpoint {
    static getChangeStateEndpoint(){
        return '/change/state';
    };
    static getCorrectStateEndpoint(){
        return '/correct-rotation';
    };

    static getDeviceInfoEndpoint() {
        return '/device/device-info'
    }

    static getMoveStepBlindsEndpoint() {
        return '/api/device/set-step-rotation';
    }

    static getMovePercentBlindsEndpoint() {
        return '/api/device/set-percent-rotation';
    }
}