((w,d)=>{
    'use strict';

    w.mainTools = {
        req: new XMLHttpRequest(),

        sendRequest: function (json_data) {
            this.req.onload = this.onLoadRequest
            this.req.open("POST", "/api/", true)
            this.req.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
            this.req.send(JSON.stringify(json_data))
        },
        
        onLoadRequest: function() {
            let res = JSON.parse(this.responseText)

            tttObject.loadBoard(res.board)
        }
    }

    console.info('[mainTools] loaded');
})(window,document)