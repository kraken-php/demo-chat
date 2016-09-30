/**
 * Helpers
 */
;(function(undefined) {

    var $D = function(id) {
        return document.querySelector(id);
    };

    this.$D = $D;

}).call(window);

/**
 * WsConnection
 */
;(function(undefined) {

    var WsConnection = function() {
        Object.defineProperty(this, 'flags', {
            value: {
                isConnected: false
            },
            writable: true
        });
        Object.defineProperty(this, 'ws', {
            value: null,
            writable: true
        });
    };

    this.WsConnection = WsConnection;

}).call(window);

/**
 * WsSocket
 */
;(function(undefined) {

    var WsSocket = function() {
        Object.defineProperty(this, 'connection', {
            value: new WsConnection()
        });
    };

    WsSocket.prototype.connect = function() {
        if (WebSocket === undefined)
        {
            throw new Error("WsConnection.connect() : Browser does not support WebSocket object.");
        }
        else if (this.connection.flags.isConnected)
        {
            throw new Error("WsConnection.connect() : Connection is already created. Disconnect before creating a new one.");
        }

        try
        {
            this.connection.ws = new WebSocket("ws://localhost:6080/chat");
        }
        catch (err)
        {
            this.onError(this.connection.ws, err);
        }

        var sock = this;
        this.connection.ws.onmessage = function(message) {
            return sock.onMessage(sock, message);
        };
        this.connection.ws.onopen = function(message) {
            return sock.onOpen(sock);
        };
        this.connection.ws.onclose = function() {
            return sock.onClose(sock);
        };
        this.connection.ws.onerror = function(err) {
            return sock.onError(sock, err);
        };
    };

    WsSocket.prototype.disconnect = function() {
        if (!this.connection.flags.isConnected)
        {
            return;
        }
        this.connection.ws.close();
    };

    WsSocket.prototype.send = function(message) {
        if (!this.connection.flags.isConnected)
        {
            return;
        }
        this.connection.ws.send(message);
    };

    WsSocket.prototype.onMessage = function(sock, message) {
        this.message(sock, message);
    };

    WsSocket.prototype.onOpen = function(sock) {
        this.connection.flags.isConnected = true;
        this.open(sock);
    };

    WsSocket.prototype.onClose = function(sock) {
        this.connection.flags.isConnected = false;
        this.close(sock);
    };

    WsSocket.prototype.onError = function(sock, err) {
        this.error(sock, err);
    };

    WsSocket.prototype.message = function(sock, message) {};

    WsSocket.prototype.open = function(sock) {};

    WsSocket.prototype.close = function(sock) {};

    WsSocket.prototype.error = function(sock, err) {};

    this.WsSocket = WsSocket;

}).call(window);

/**
 * Chat
 */
;(function(undefined) {

    var Chat = function() {};

    Chat.prototype.create = function() {
        var height;

        height = $D('#kraken-toolbar').offsetHeight + $D('#kraken-inputbox').offsetHeight;
        height = document.body.clientHeight - height;

        $D('#chat-messagebox').style.height = height + 'px';
        $D('#chat-users').innerHTML = '';
        $D('#chat-messagebox').innerHTML = '';
    };

    Chat.prototype.initUI = function(data) {
        var name  = '#' + data.id;
        var color = data.color;

        var div = $D('#info-user .info-name');

        div.innerHTML = name;
        div.style.backgroundColor = color;
        div.style.color = (new ColorPicker).invertCssColor(color);
    };

    Chat.prototype.createUser = function(id, sender, color) {
        var node;

        node = document.createElement('div');
        node.className = 'chat-user';
        node.style.backgroundColor = color;
        node.style.color = (new ColorPicker).invertCssColor(color);
        node.id = 'user-' + id;
        node.innerHTML = sender;

        $D('#chat-users').appendChild(node);
    };

    Chat.prototype.removeUser = function(id) {
        $D('#user-' + id).remove();
    };

    Chat.prototype.createMessage = function(id, sender, color, date, message) {
        var node,
            infoDiv,
            nameDiv,
            dateDiv,
            mssgDiv;

        node = document.createElement('div');
        node.className = 'chat-message';

        infoDiv = document.createElement('div');
        nameDiv = document.createElement('div');
        dateDiv = document.createElement('div');
        mssgDiv = document.createElement('div');

        nameDiv.className = 'name';
        nameDiv.style.backgroundColor = color;
        nameDiv.style.color = (new ColorPicker).invertCssColor(color);
        nameDiv.innerHTML = sender;

        dateDiv.className = 'date';
        dateDiv.innerHTML = date;

        infoDiv.className = 'info';
        infoDiv.appendChild(nameDiv);
        infoDiv.appendChild(dateDiv);

        mssgDiv.className = 'message bubble';
        mssgDiv.innerHTML = message;

        node.appendChild(infoDiv);
        node.appendChild(mssgDiv);

        $D('#chat-messagebox').appendChild(node);
    };

    this.Chat = Chat;

}).call(window);

/**
 * ColorPicker
 */
;(function(undefined) {

    var ColorPicker = function() {};

    ColorPicker.prototype.invertCssColor = function(color) {
        var rgb = this.invertColor(this.hexColor2rgb(color));
        return this.rgb2hexColor(rgb);
    };

    ColorPicker.prototype.invertColor = function(rgb) {
        var yuv = this.rgb2yuv(rgb);
        var factor = 180;
        var threshold = 120;
        yuv.y = this.clamp(yuv.y + (yuv.y > threshold ? -factor : factor));
        return this.yuv2rgb(yuv);
    };

    ColorPicker.prototype.rgb2hexColor = function(rgb) {
        return '#' + this.dec2hex(rgb.r) + this.dec2hex(rgb.g) + this.dec2hex(rgb.b);
    };

    ColorPicker.prototype.hexColor2rgb = function(color) {
        color = color.substring(1); // remove #
        return {
            r: parseInt(color.substring(0, 2), 16),
            g: parseInt(color.substring(2, 4), 16),
            b: parseInt(color.substring(4, 6), 16)
        };
    };

    ColorPicker.prototype.rgb2hexColor = function(rgb) {
        return '#' + this.dec2hex(rgb.r) + this.dec2hex(rgb.g) + this.dec2hex(rgb.b);
    };

    ColorPicker.prototype.dec2hex = function(n) {
        var hex = n.toString(16);
        if (hex.length < 2) {
            return '0' + hex;
        }
        return hex;
    };

    ColorPicker.prototype.rgb2yuv = function(rgb) {
        var y = this.clamp(rgb.r *  0.29900 + rgb.g *  0.587   + rgb.b * 0.114);
        var u = this.clamp(rgb.r * -0.16874 + rgb.g * -0.33126 + rgb.b * 0.50000 + 128);
        var v = this.clamp(rgb.r *  0.50000 + rgb.g * -0.41869 + rgb.b * -0.08131 + 128);
        return { y:y, u:u, v:v };
    };

    ColorPicker.prototype.yuv2rgb = function(yuv) {
        var y = yuv.y;
        var u = yuv.u;
        var v = yuv.v;
        var r = this.clamp(y + (v - 128) *  1.40200);
        var g = this.clamp(y + (u - 128) * -0.34414 + (v - 128) * -0.71414);
        var b = this.clamp(y + (u - 128) *  1.77200);
        return { r:r, g:g, b:b };
    };

    ColorPicker.prototype.clamp = function(n) {
        if (n<0) { return 0;}
        if (n>255) { return 255;}
        return Math.floor(n);
    };

    this.ColorPicker = ColorPicker;

}).call(window);

/**
 * Application
 */
;(function(undefined) {

    var Application = function() {
        Object.defineProperty(this, 'panel', {
            value: {
                load: $D('#kraken-panel'),
                appt: $D('#kraken-app-panel'),
                text: $D('#chat-text')
            }
        });
        Object.defineProperty(this, 'ws', {
            value: new WsSocket()
        });
        Object.defineProperty(this, 'chat', {
            value: new Chat()
        });
        this.start();
    };

    Application.prototype.start = function() {
        console.log('Chat is being loaded...');
        var app = this;
        this.ws.open = function(sock) {
            app.showChat();
            app.chat.create();
            app.panel.text.onkeyup = function(e) {
                var key = e.keyCode;
                var text = '';

                if (key == 13)
                {
                    text = app.panel.text.value;
                    if (text !== "\n")
                    {
                        var message = {};
                        message['type'] = 'message';
                        message['data'] = text.slice(0, -1);

                        app.ws.send(JSON.stringify(message));
                        app.panel.text.value = '';
                    }
                }
                else
                {
                    return true;
                }
            };
            console.log('Chat opened.');
        };
        this.ws.close = function(sock) {
            app.hideChat();
            console.log('Chat closed.');
        };
        this.ws.message = function(sock, message) {
            var data = JSON.parse(message.data);

            switch (data.type) {
                case 'message':
                    app.chat.createMessage(data.data.id, data.data.name, data.data.color, data.data.date, data.data.mssg);
                    break;

                case 'connect':
                    app.chat.createUser(data.data.id, '#' + data.data.id, data.data.color);
                    break;

                case 'disconnect':
                    app.chat.removeUser(data.data.id);
                    break;

                case 'init':
                    app.chat.initUI(data.data);
                    data.data.users.forEach(function(el, key) {
                        app.chat.createUser(el.id, '#' + el.id, el.color);
                    });
                    break;

                default:
            }
        };
        this.ws.error = function(sock, err) {
            console.log('Error' + err);
        };
        this.ws.connect();
    };

    Application.prototype.stop = function() {
        this.ws.close();
    };

    Application.prototype.showChat = function() {
        this.panel.load.style.display = 'none';
        this.panel.appt.style.display = 'block';
    };

    Application.prototype.hideChat = function() {
        this.panel.load.style.display = 'block';
        this.panel.appt.style.display = 'none';
    };

    this.Application = Application;

}).call(window);

var app = new Application();
