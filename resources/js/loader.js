(function () {
    const loader = document.getElementById("global-loader");
    if (loader) {
        loader.classList.add("play");
        loader.classList.remove("is-hidden");
        const duration = 2000;
        setTimeout(() => {
            loader.classList.remove("play");
            loader.classList.add("is-hidden");
        }, duration);
    }
})();
