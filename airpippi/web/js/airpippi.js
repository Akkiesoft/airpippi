function showMessage(itemId, className, msg, timeout) {
    document.getElementById(itemId).innerHTML = msg;
    document.getElementById(itemId).className = className;
    if (0 <= timeout) {
        setTimeout("hiddenItem('" + itemId + "')", timeout);
    }
    return false;
}

function hiddenItem(item) {
    document.getElementById(item).className = "visually-hidden";
    return false;
}

async function myfetch(url = '', opt = {}) {
    const j = fetch(url, opt)
        .then(r => {
            if (r.ok) {
                hiddenItem('ret_top');
            } else {
                if (r.status == 502)
                    throw new Error("APIサーバーが停止しているようです");
                else
                    throw new Error("なにかエラーがおきました");
            }
            return r.json();
        })
        .catch(e => {
            showMessage("ret_top", "alert alert-danger mb-5", e, -1);
            return { "result": "error" }
        });
    return j;
}

// ZARU
function zerofill_pre(n, d) { return ("0" + n).slice(d * -1); }