window.defaultUrl = `${baseUrl}Dashboard/`;

$(document).ready(function () {
    
    new Highcharts.Chart({
        chart: {
            renderTo: 'chart_graph',
            type: 'line',
        },
        title: {
            text: 'Grafik Penerimaan Per Hari',
            x: -20
        },
        // subtitle: {
        // text: 'Total uang yang didapat dalam tiap bulan',
        // x: -20
        // },
        // 
        xAxis: {
            categories: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24"]
        },
        yAxis: {
            title: {
                text: 'Total Penerimaan'
            },
            min: 0,
            //		            tickInterval: 300,
            labels: {
                formatter: function () {
                    return Highcharts.numberFormat(this.value, 0);
                }
            }
        },
        series: [{
            name: 'Total ',
            data: [1027475623, 968756584, 758615743, 505011524, 411173127, 1003286803, 765318472, 663129021, 515998146, 703161083, 361071692, 255573192, 900737332, 1070507996, 885391575, 1057355439, 4154262934, 356077227, 444743961, 1444677055, 463994642, 363203890, 233827759, 63247875]
        }]
    });


    new Highcharts.Chart({
        chart: {
            renderTo: 'pieChart',
            type: 'pie'
        },
        title: {
            text: 'Penerimaan Bulan Ini'
        },
        plotOptions: {
            pie: {
                innerSize: 130,
                depth: 60
            }
        },
        series: [{
            name: 'Total Penerimaan',
            data: [
                ['Terbayar', 16564972091],
                ['Sisa', 4825716134],
            ]
        }]
    });

    Highcharts.chart('pieChart', {
        chart: {
            backgroundColor: 'transparent',
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false,
            height: 505,
            events: {
                render: function () {
                    var chart = this,
                        sum = 0;

                    if (chart.textGroup) {
                        chart.textGroup.destroy()
                        chart.textGroup = undefined;
                    }

                    chart.textGroup = chart.renderer.g('textGroup').add().toFront();
                    let persenKini = chart.series[0].data[0].persenKini;
                    chart.series[0].data.forEach(function (value) {
                        // pilih = value.y;
                        sum += value.y;
                    });
                    // let persenDiTengah = chart.series[0].data,
                    var customText = chart.myCustomText = chart.renderer.text(
                            `Rp. ` + Number(sum).toLocaleString('id-ID') + "", chart.plotWidth / 2.3, chart.plotHeight / 2
                        )
                        .css({
                            fontSize: '15px',
                            fontWeight: '600',
                            color: 'black'
                        })
                        .add(chart.textGroup);
                    chart.textGroup.translate((14 / sum.toString().length) * -7, 50);
                }
            }
        },
        title: {
            text: 'Penerimaan Bulan Ini',
            // align: 'center',
            // verticalAlign: 'top',
            margin: 20
            // y: 60
        },
        // legend: {
        //     labelFormatter: function () {
        //         var legendName = this.name;
        //         return legendName.replace(' (', '<br>(');
        //         var match = legendName.match(/.{1,21}/g);
        //         return match.toString().replace(/\,/g, "<br/>");
        //     },
        //     layout: 'vertical',
        //     align: 'right',
        //     verticalAlign: 'middle',
        //     itemMarginTop: 2,
        //     itemMarginBottom: 2,
        //     itemMarginLeft: 2,
        //     // x: 60,
        //     itemStyle: {
        //         color: '#FFFFFF',
        //         fontSize: '11.5px',
        //     },
        //     navigation: {
        //         activeColor: 'white',
        //         inactiveColor: 'white',
        //         style: {
        //             fontWeight: 'bold',
        //             color: '#ffffff',
        //             fontSize: '12px'
        //         }
        //     },
        // },
        tooltip: {
            enabled: true,
            formatter: function () {
                return this.key + "<br/><b> Jumlah : " + Number(this.y).toLocaleString('id-ID') + "</b>";
            },
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        // plotOptions: {
        //     pie: {
        //         allowPointSelect: true,
        //         cursor: "pointer",
        //         dataLabels: {
        //             enabled: false,
        //         },
        //         showInLegend: true,
        //         dataLabels: {
        //             enabled: false,
        //             format: '{point.percentage:.1f} %',
        //         }
        //     },
        plotOptions: {
            // pie: {
            //     allowPointSelect: true,
            //     dataLabels: {
            //         enabled: false,
            //     },
            //     showInLegend: true,
            //     // size: 170,
            //     borderColor: 'transparent'
            // }
        },
        credits: {
            enabled: false
        },
        series: [{
            type: 'pie',
            innerSize: '70%',
            data: [
                ['Terbayar', 16550553341],
                ['Sisa', 4840134884],
            ]
        }]
    });

    // new Highcharts.Chart({
    //     chart: {
    //         renderTo: 'pieChart',
    //         type: 'pie',
    //         height: 200
    //     },
    //     title: {
    //         text: 'Penerimaan Bulan Ini'
    //     },
    //     plotOptions: {
    //         pie: {
    //             innerSize: 130,
    //             depth: 60
    //         }
    //     },
    //     series: [{
    //         name: 'Total Penerimaan',
    //         data: [
    //             ['Terbayar', 16550553341],
    //             ['Sisa', 4840134884],
    //         ]
    //     }]
    // });
});