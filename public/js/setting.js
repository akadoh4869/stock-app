
function openOverlay(id) {
    document.getElementById(id).style.display = 'flex';
}

function closeOverlay(id) {
    document.getElementById(id).style.display = 'none';
}

function clearAppCache() {
    caches.keys().then(function (names) {
        for (let name of names) caches.delete(name);
    }).then(() => {
        alert('キャッシュをクリアしました');
    });
}

