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
                d.tanggal_mulai = $("#tanggal_mulai").val() ?? null;
                d.tanggal_selesai = $("#tanggal_selesai").val() ?? null;
                d.pembayaran_id = $("#pembayaran_id").val() ?? null;
            },
        },
        columns: columns,
        lengthMenu: [
            [50, 100, 250, -1],
            [50, 100, 250, "All"],
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
    $(`#${targetId} input[type="checkbox"]`)
        .prop("checked", false)
        .trigger("change");

    const cekLabelModal = $("#label-modal");
    if (cekLabelModal) {
        $("#id").val("");
        cekLabelModal.text("Tambah");
    }

    if (url) {
        cekLabelModal.text("Edit");
        const successCallback = function (response) {
            fields.forEach((field) => {
                if (response.data[field]) {
                    $(`#${targetId} #${field}`)
                        .val(response.data[field])
                        .trigger("change");
                }
            });

            if (response.data.hak_akses) {
                const hakAkses = response.data.hak_akses;
                $(`#${targetId} #tambah_pengunjung_masuk`)
                    .prop("checked", hakAkses.tambah_pengunjung_masuk == 1)
                    .trigger("change");
                $(`#${targetId} #tambah_pengunjung_murid`)
                    .prop("checked", hakAkses.tambah_pengunjung_murid == 1)
                    .trigger("change");
                $(`#${targetId} #tambah_pengunjung_keluar`)
                    .prop("checked", hakAkses.tambah_pengunjung_keluar == 1)
                    .trigger("change");
                $(`#${targetId} #riwayat_pengunjung_masuk`)
                    .prop("checked", hakAkses.riwayat_pengunjung_masuk == 1)
                    .trigger("change");
                $(`#${targetId} #riwayat_pengunjung_keluar`)
                    .prop("checked", hakAkses.riwayat_pengunjung_keluar == 1)
                    .trigger("change");
                $(`#${targetId} #laporan_keuangan`)
                    .prop("checked", hakAkses.laporan_keuangan == 1)
                    .trigger("change");
                $(`#${targetId} #user_management`)
                    .prop("checked", hakAkses.user_management == 1)
                    .trigger("change");
                $(`#${targetId} #ubah_tarif`)
                    .prop("checked", hakAkses.ubah_tarif == 1)
                    .trigger("change");
                $(`#${targetId} #daftar_bank`)
                    .prop("checked", hakAkses.daftar_bank == 1)
                    .trigger("change");
                $(`#${targetId} #toleransi_waktu`)
                    .prop("checked", hakAkses.toleransi_waktu == 1)
                    .trigger("change");
                $(`#${targetId} #murid`)
                    .prop("checked", hakAkses.murid == 1)
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
            option.text(
                "Anak : " +
                    row.nama_anak +
                    " - Orang Tua : " +
                    row.nama_orang_tua
            );
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

const select2ToJsonMurid = () => {
    const selectElem = $("#murid_id").empty();

    const successCallback = function (response) {
        selectElem.empty();

        const emptyOption = $("<option></option>");
        emptyOption.attr("value", "");
        emptyOption.text("-- Pilih Murid --");
        selectElem.append(emptyOption);

        const responseList = response.data;
        responseList.forEach(function (row) {
            const option = $("<option></option>");
            option.attr("value", row.id);
            option.text(
                "Anak : " +
                    row.nama_anak +
                    " - Orang Tua : " +
                    row.nama_orang_tua
            );
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
        "/murid",
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

const getTicketNow = () => {
    const successCallback = function (response) {
        const kode = $("#now").val();
        if (response.data) {
            if (response.data.id != kode) {
                $("#now").val(response.data.id);
                $("#label-tiket").text(response.data.label);
                $("#detail").html(`
                    <div clas="py-4">
                        <div class="mb-3 text-center">
                            <img src="/storage/${
                                response.data.label == "Pengunjung Masuk"
                                    ? "pengunjung_masuk"
                                    : "pengunjung_keluar"
                            }/${response.data.qr_code}"
                                width="150px">
                        </div>
                        <table class="table table-striped table-sm" width="100%">
                            <tbody>
                                <tr>
                                    <td width="30%">Nama Anak</td>
                                    <td width="2%">:</td>
                                    <td>${response.data.nama_anak}</td>
                                </tr>
                                <tr>
                                    <td width="30%">Orang Tua</td>
                                    <td width="2%">:</td>
                                    <td>${response.data.nama_orang_tua}</td>
                                </tr>
                                <tr>
                                    <td>Jenis Kelamin</td>
                                    <td>:</td>
                                    <td>${response.data.jenis_kelamin}</td>
                                </tr>
                                ${
                                    response.data.metode_pembayaran
                                        ? `
                                <tr>
                                    <td>Pembayaran</td>
                                    <td>:</td>
                                    <td>${response.data.metode_pembayaran}</td>
                                </tr>


                                `
                                        : ""
                                }
                                <tr>
                                    <td>Bermain</td>
                                    <td>:</td>
                                    <td>${response.data.durasi_bermain} Jam</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                `);
            }
        } else {
            $("#detail").html(`
                <div class="text-center py-5">
                    <i class="ti ti-reload text-danger me-1"></i>
                    Belum Ada Tiket
                </div>
            `);
        }
    };

    const errorCallback = function (error) {
        $("#detail").html(`
            <div class="text-center py-5">
                <i class="ti ti-reload text-danger me-1"></i>
                Belum Ada Tiket
            </div>
        `);
    };

    ajaxCall("/get-tiket-now", "GET", null, successCallback, errorCallback);
};
