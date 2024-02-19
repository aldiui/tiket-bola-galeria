const datatableCall = (targetId, url, columns) => {
    $(`#${targetId}`).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: url,
            type: "GET",
            data: function (d) {
                d.mode = "datatable";
                d.bulan = $("#bulan_filter").val() ?? null;
                d.tahun = $("#tahun_filter").val() ?? null;
                d.tanggal = $("#tanggal_filter").val() ?? null;
            },
        },
        columns: columns,
        lengthMenu: [
            [25, 50, 100, 250, -1],
            [25, 50, 100, 250, "All"],
        ],
    });
};

const ajaxCall = (url, method, data, successCallback, errorCallback) => {
    $.ajax({
        type: method,
        enctype: "multipart/form-data",
        url,
        cache: false,
        data,
        contentType: false,
        processData: false,
        headers: {
            Accept: "application/json",
            "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content"),
        },
        dataType: "json",
        success: function (response) {
            successCallback(response);
        },
        error: function (error) {
            errorCallback(error);
        },
    });
};

const getModal = (targetId, url = null, fields = null) => {
    $(`#${targetId}`).modal("show");
    $(`#${targetId} .form-control`).removeClass("is-invalid");
    $(`#${targetId} .invalid-feedback`).html("");

    if (url) {
        const successCallback = function (response) {
            fields.forEach((field) => {
                if (response.data[field]) {
                    $(`#${targetId} #${field}`)
                        .val(response.data[field])
                        .trigger("change");
                }
            });

            if (response.data.hak_akses) {
                $(`#${targetId} #tambah_pengunjung_masuk_edit`)
                    .prop(
                        "checked",
                        response.data.hak_akses.tambah_pengunjung_masuk
                    )
                    .trigger("change");
                $(`#${targetId} #tambah_pengunjung_keluar_edit`)
                    .prop(
                        "checked",
                        response.data.hak_akses.tambah_pengunjung_keluar
                    )
                    .trigger("change");
                $(`#${targetId} #riwayat_pengunjung_masuk_edit`)
                    .prop(
                        "checked",
                        response.data.hak_akses.riwayat_pengunjung_masuk
                    )
                    .trigger("change");
                $(`#${targetId} #riwayat_pengunjung_keluar_edit`)
                    .prop(
                        "checked",
                        response.data.hak_akses.riwayat_pengunjung_keluar
                    )
                    .trigger("change");
                $(`#${targetId} #laporan_keuangan_edit`)
                    .prop("checked", response.data.hak_akses.laporan_keuangan)
                    .trigger("change");
                $(`#${targetId} #user_management_edit`)
                    .prop("checked", response.data.hak_akses.user_management)
                    .trigger("change");
                $(`#${targetId} #ubah_tarif_edit`)
                    .prop("checked", response.data.hak_akses.ubah_tarif)
                    .trigger("change");
            }
        };

        const errorCallback = function (error) {
            console.log(error);
        };
        ajaxCall(url, "GET", null, successCallback, errorCallback);
    }
    $(`#${targetId} .form-control`).val("");
};

const handleSuccess = (
    response,
    dataTableId = null,
    modalId = null,
    redirect = null
) => {
    const successToast = (title) => {
        Swal.fire({
            title: title,
            icon: "success",
            text: response.message,
            timer: 2000,
            showConfirmButton: false,
        });
    };

    if (dataTableId !== null) {
        successToast("Berhasil");
        $(`#${dataTableId}`).DataTable().ajax.reload();
    }

    if (modalId !== null) {
        $(`#${modalId}`).modal("hide");
    }

    if (redirect !== null) {
        if (redirect === "no") {
            successToast("Berhasil");
        } else {
            successToast("Berhasil");
            setTimeout(() => {
                window.location.href = redirect;
            }, 2000);
        }
    }
};

const handleValidationErrors = (error, formId = null, fields = null) => {
    if (error.responseJSON.data && fields) {
        fields.forEach((field) => {
            if (error.responseJSON.data[field]) {
                $(`#${formId} #${field}`).addClass("is-invalid");
                $(`#${formId} #error${field}`).html(
                    error.responseJSON.data[field][0]
                );
            } else {
                $(`#${formId} #${field}`).removeClass("is-invalid");
                $(`#${formId} #error${field}`).html("");
            }
        });
    } else {
        Swal.fire({
            title: "Gagal",
            icon: "error",
            text: error.responseJSON.message,
            timer: 2000,
            showConfirmButton: false,
        });
    }
};

const confirmDelete = (url, tableId) => {
    Swal.fire({
        title: "Apakah Kamu Yakin?",
        text: "Ingin menghapus data ini!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, hapus!",
    }).then((result) => {
        if (result.isConfirmed) {
            const data = null;

            const successCallback = function (response) {
                handleSuccess(response, tableId, null);
            };

            const errorCallback = function (error) {
                console.log(error);
            };

            ajaxCall(url, "DELETE", data, successCallback, errorCallback);
        }
    });
};
const confirmStart = (url, tableId) => {
    Swal.fire({
        title: "Apakah Kamu Yakin?",
        text: "Ingin menjalankan tiket ini!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, Jalankan!",
    }).then((result) => {
        if (result.isConfirmed) {
            const data = null;

            const successCallback = function (response) {
                handleSuccess(response, tableId, null);
            };

            const errorCallback = function (error) {
                console.log(error);
            };

            ajaxCall(url, "POST", data, successCallback, errorCallback);
        }
    });
};

const setButtonLoadingState = (buttonSelector, isLoading, title = "Simpan") => {
    const buttonText = isLoading
        ? `<div class="spinner-border spinner-border-sm me-2" role="status">
            </div>
         ${title}`
        : title;
    $(buttonSelector).prop("disabled", isLoading).html(buttonText);
};

const select2ToJsonPengunjungMasuk = () => {
    const selectElem = $("#pengunjung_masuk_id").empty();

    const successCallback = function (response) {
        selectElem.empty();

        const emptyOption = $("<option></option>");
        emptyOption.attr("value", "");
        emptyOption.text("-- Pilih Pengunjung Masuk --");
        selectElem.append(emptyOption);

        const responseList = response.data;
        responseList.forEach(function (row) {
            const option = $("<option></option>");
            option.attr("value", row.id);
            option.text(row.nama_anak + " - " + row.nama_orang_tua);
            selectElem.append(option);
        });

        selectElem.select2({
            theme: "bootstrap-5",
            width: "100%",
        });
    };

    const errorCallback = function (error) {
        console.log(error);
    };

    ajaxCall(
        "/riwayat-pengunjung-masuk",
        "GET",
        null,
        successCallback,
        errorCallback
    );
};

let chart = null;

const renderSingleChart = (data, labels, chartType = "Laporan Keuangan") => {
    if (chart) {
        chart.destroy();
    }

    const options = {
        series: [
            {
                name: chartType,
                data: data,
            },
        ],
        chart: {
            height: 350,
            type: "area",
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: "smooth",
        },
        xaxis: {
            type: "string",
            categories: labels,
        },
    };

    chart = new ApexCharts($("#chart")[0], options);
    chart.render();
};

const renderMultipleChart = (dataMasuk, dataKeluar, labels) => {
    if (chart) {
        chart.destroy();
    }

    const options = {
        series: [
            {
                name: "Pengunjung Masuk",
                data: dataMasuk,
            },
            {
                name: "Pengunjung Keluar",
                data: dataKeluar,
            },
        ],
        chart: {
            height: 350,
            type: "area",
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: "smooth",
        },
        xaxis: {
            type: "string",
            categories: labels,
        },
    };

    chart = new ApexCharts($("#chart")[0], options);
    chart.render();
};

const renderPieChart = (seriesData) => {
    if (chart) {
        chart.destroy();
    }

    var options = {
        series: seriesData,
        chart: {
            width: 350,
            type: "pie",
        },
        labels: ["Laki-laki", "Perempuan"],
        responsive: [
            {
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200,
                    },
                    legend: {
                        position: "bottom",
                    },
                },
            },
        ],
    };

    chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
};

const updateCountdown = (targetElement, duration) => {
    const countdownElement = $(targetElement);
    let durationDifference = parseDuration(duration);

    function parseDuration(duration) {
        const parts = duration.split(":");
        return {
            hours: parseInt(parts[0]),
            minutes: parseInt(parts[1]),
            seconds: parseInt(parts[2]),
        };
    }

    const formatTime = (hours, minutes, seconds) => {
        return pad(hours) + ":" + pad(minutes) + ":" + pad(seconds);
    };

    const pad = (num) => {
        return num < 10 ? "0" + num : num;
    };

    const updateTimer = () => {
        if (
            durationDifference.hours === 0 &&
            durationDifference.minutes === 0 &&
            durationDifference.seconds === 0
        ) {
            countdownElement.text("00:00:00");
        } else {
            durationDifference.seconds--;
            if (durationDifference.seconds < 0) {
                durationDifference.seconds = 59;
                durationDifference.minutes--;
                if (durationDifference.minutes < 0) {
                    durationDifference.minutes = 59;
                    durationDifference.hours--;
                    if (durationDifference.hours < 0) {
                        durationDifference.hours = 0;
                    }
                }
            }
            countdownElement.text(
                formatTime(
                    durationDifference.hours,
                    durationDifference.minutes,
                    durationDifference.seconds
                )
            );
            setTimeout(updateTimer, 1000);
        }
    };

    updateTimer();
};
