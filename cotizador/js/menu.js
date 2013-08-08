<!--
var caution = false
function setCookie(name, value, expires, path, domain, secure) {
        var curCookie = name + "=" + escape(value) +
                ((expires) ? "; expires=" + expires.toGMTString() : "") +
                ((path) ? "; path=" + path : "") +
                ((domain) ? "; domain=" + domain : "") +
                ((secure) ? "; secure" : "")
        if (!caution || (name + "=" + escape(value)).length <= 4000)
                document.cookie = curCookie
        else
                if (confirm("Cookie exceeds 4KB and will be cut!"))
                        document.cookie = curCookie
}

function getCookie(name) {
        var prefix = name + "="
        var cookieStartIndex = document.cookie.indexOf(prefix)
        if (cookieStartIndex == -1)
                return null
        var cookieEndIndex = document.cookie.indexOf(";", cookieStartIndex + prefix.length)
        if (cookieEndIndex == -1)
                cookieEndIndex = document.cookie.length
        return unescape(document.cookie.substring(cookieStartIndex + prefix.length, cookieEndIndex))
}

function deleteCookie(name, path, domain) {
        if (getCookie(name)) {
                document.cookie = name + "=" + 
                ((path) ? "; path=" + path : "") +
                ((domain) ? "; domain=" + domain : "") +
                "; expires=Thu, 01-Jan-70 00:00:01 GMT"
        }
}

function fixDate(date) {
        var base = new Date(0)
        var skew = base.getTime()
        if (skew > 0)
                date.setTime(date.getTime() - skew)
}

function item(parent, text, depth) {
        this.parent = parent 
        this.text = text 
        this.depth = depth 
}

function makeArray(length) {
        this.length = length 
}

function makeDatabase() {
        outline = new makeArray(12)
        outline[0] = new item(true, 'Menu', 0)
        outline[1] = new item(false, '<A HREF="#">Mi Vestmed</A>', 1)
        outline[2] = new item(false, '<A HREF="#">Cotizaciones</A>', 1)
        outline[3] = new item(true, 'Clientes', 1)
        outline[4] = new item(false, '<A HREF="javascript:NuevoCliente()">Nuevo</A>', 2)
        outline[5] = new item(false, '<A HREF="javascript:BuscarCliente(\'mnu\')">Buscar</A>', 2)
        outline[6] = new item(false, '<A HREF="javascript:Editar()">Editar</A>', 2)
        outline[7] = new item(false, '<A HREF="javascript:Historico()">Historico</A>', 2)
        outline[8] = new item(false, '<A HREF="#">Ventas</A>', 1)
        outline[9] = new item(false, '<A HREF="#">Compras</A>', 1)
        outline[10] = new item(false, '<A HREF="#">Despacho</A>', 1)
        outline[11] = new item(false, '<A HREF="#">Bordados</A>', 1)

        setStates()
        setImages()
}

function setStates() {
        var storedValue = getCookie("menuvestmed")
        if (!storedValue) {
                for (var i = 0; i < outline.length; ++i) {
                        if (outline[i].depth == 0)
                                outline[i].state = true
                        else
                                outline[i].state = false
                }
        } else {
                // extract current states from cookie (0 => false, 1 => true)
                for (var i = 0; i < outline.length; ++i) {
                        if (storedValue.charAt(i) == '1')
                                outline[i].state = true
                        else
                                outline[i].state = false
                }
        }
}

function setImages() {
        for (var i = 0; i < outline.length; ++i) {
                if (outline[i].state)
                        if (outline[i].parent) 
                                if (outline[i + 1].state) // outline[i] is exploded
                                        outline[i].pic = '<A HREF="javascript:toggle(' + i + ')"><IMG SRC="exploded.gif" BORDER=0></A>'
                                else 
                                        outline[i].pic = '<A HREF="javascript:toggle(' + i + ')"><IMG SRC="collapsd.gif" BORDER=0></A>'
                        else // outline[i] is only a child (not a parent)
                                outline[i].pic = '<IMG SRC="child.gif" BORDER=0>'
        }
}

function toggle(num) {
        for (var i = num + 1; i < outline.length && outline[i].depth >= outline[num].depth + 1; ++i) {
                if (outline[i].depth == outline[num].depth + 1)
                        outline[i].state = !outline[i].state // toggle state
        }
        setStorage()
        history.go(0)
}

function setStorage() {
        var text = ""
        for (var i = 0; i < outline.length; ++i) {
                text += (outline[i].state) ? "1" : "0"
        }
        setCookie("menuvestmed", text)
}

makeDatabase()
// -->
