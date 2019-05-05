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

    static getMoveBlindsEndpoint() {
        return '/move';
    }
}