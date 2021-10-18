$(function () {
    qtyStockByBrands();
    qtyInStock();
    priceInStock();
});

const qtyStockByBrands = () => {
    $.get(`${window.location.origin}/admin/ajax/automoveis/qtd-estoque-por-marcas`, function (response) {

        const total = response.total;
        let calcProgress;

        $.each(response.data, function (key, value) {
            calcProgress = (value * 100) / total;
            $('#qtyStockByBrands').append(`
                <div class="progress-group">
                    ${key}
                    <span class="float-right"><b>${value}</b>/${total}</span>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-primary" style="width: ${calcProgress}%"></div>
                    </div>
                </div>
            `);
        });
    });
}

const qtyInStock = () => {
    $.get(`${window.location.origin}/admin/ajax/automoveis/qtd-estoque-por-tipo-de-automovel`, function (response) {

        $.each(response, function (key, value) {
            $('#qtyInStock').append(`
                <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                    <p class="text-primary text-xl">
                        <i class="${value.icon}"></i>
                    </p>
                    <p class="d-flex flex-column text-right">
                        <span class="font-weight-bold">
                          ${value.value}
                        </span>
                        <span class="text-muted">${key}</span>
                    </p>
                </div>
            `);
        });
    });
}

const priceInStock = () => {
    $.get(`${window.location.origin}/admin/ajax/automoveis/valor-estoque-por-tipo-de-automovel`, function (response) {

        $.each(response, function (key, value) {
            $('#priceInStock').append(`
                <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                    <p class="text-primary text-xl">
                        <i class="${value.icon}"></i>
                    </p>
                    <p class="d-flex flex-column text-right">
                        <span class="font-weight-bold">
                          ${value.value}
                        </span>
                        <span class="text-muted">${key}</span>
                    </p>
                </div>
            `);
        });
    });
}
