$(function () {
    //Widgets count
    $('.count-to').countTo();

    //Sales count to
    $('.sales-count-to').countTo({
        formatter: function (value, options) {
            return '$' + value.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, ' ').replace('.', ',');
        }
    });

    initRealTimeChart();
    initDonutChart();
    initSparkline();
});

var realtime = 'on';
function initRealTimeChart() {
    //Real time ==========================================================================================
    var plot = $.plot('#real_time_chart', [getRandomData()], {
    // var plot = $.plot('#real_time_chart', currNumCar + currNumTruck, {
        series: {
            shadowSize: 0,
            color: 'rgb(0, 188, 212)'
        },
        grid: {
            borderColor: '#f3f3f3',
            borderWidth: 1,
            tickColor: '#f3f3f3'
        },
        lines: {
            fill: true
        },
        yaxis: {
            min: 0,
            max: 40
        },
        xaxis: {
            min: 0,
            max: 20
        }
    });

    function updateRealTime() {
        plot.setData([getRandomData()]);
        console.log("currNumCar: " + currNumCar);
        console.log("currNumTruck: " + currNumTruck);
        // plot.setData(currNumCar + currNumTruck);
        plot.draw();

        var timeout;
        if (realtime === 'on') {
            timeout = setTimeout(updateRealTime, 320);
        } else {
            clearTimeout(timeout);
        }
    }

    updateRealTime();

    $('#realtime').on('change', function () {
        realtime = this.checked ? 'on' : 'off';
        updateRealTime();
    });
    //====================================================================================================
}

function initSparkline() {
    $(".sparkline").each(function () {
        var $this = $(this);
        $this.sparkline('html', $this.data());
    });
}

function initDonutChart() {
    var perNumCar = (parseInt(currNumCar)/(parseInt(currNumCar) + parseInt(currNumTruck))) * 100;
    var perNumTruck = (parseInt(currNumTruck)/(parseInt(currNumCar) + parseInt(currNumTruck))) * 100;

    Morris.Donut({
        element: 'carPieChart',
		  data: [
		    {label: "Car", value: perNumCar.toFixed(2)},
		    {label: "Truck", value: perNumTruck.toFixed(2)}
		  ],
        colors: ['rgb(0, 188, 212)', 'rgb(255, 152, 0)'],
        formatter: function (y) {
            return y + '%'
        }
    });
}



var data = [], totalPoints = 40;
function getRandomData() {
    if (data.length > 0) data = data.slice(1);

    while (data.length < totalPoints) {
        // var prev = data.length > 0 ? data[data.length - 1] : 50, y = prev + Math.random() * 10 - 5;

        // var y = 33;
        // if (y < 0) { y = 0; } else if (y > 40) { y = 40; }

        data.push(parseInt(currNumCar) + parseInt(currNumTruck));
    }

    var res = [];
    for (var i = 0; i < data.length; ++i) {
        res.push([i, data[i]]);
    }

    return res;
}

