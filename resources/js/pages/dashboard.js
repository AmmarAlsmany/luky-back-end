/**
 * Theme: Larkon - Responsive Bootstrap 5 Admin Dashboard
 * Author: Techzaa
 * Module/App: Dashboard
 */

import ApexCharts from 'apexcharts'
import 'jsvectormap'
import 'jsvectormap/dist/maps/world.js'

// Get dashboard data from window (passed from Blade)
const dashboardData = window.dashboardData || {
    overview: {},
    comparisons: {},
    topProviders: [],
    topClient: {},
    providersByRegion: []
};

//
// Conversions - Bookings Overview Chart
//
const totalBookings = dashboardData.overview.total_bookings || 0;
const completedBookings = dashboardData.overview.completed_bookings || 0;
const completionRate = totalBookings > 0 ? ((completedBookings / totalBookings) * 100).toFixed(1) : 0;

var options = {
    chart: {
        height: 292,
        type: 'radialBar',
    },
    plotOptions: {
        radialBar: {
            startAngle: -135,
            endAngle: 135,
            dataLabels: {
                name: {
                    fontSize: '14px',
                    color: "undefined",
                    offsetY: 100
                },
                value: {
                    offsetY: 55,
                    fontSize: '20px',
                    color: undefined,
                    formatter: function (val) {
                        return val + "%";
                    }
                }
            },
            track: {
                background: "rgba(170,184,197, 0.2)",
                margin: 0
            },
        }
    },
    fill: {
        gradient: {
            enabled: true,
            shade: 'dark',
            shadeIntensity: 0.2,
            inverseColors: false,
            opacityFrom: 1,
            opacityTo: 1,
            stops: [0, 50, 65, 91]
        },
    },
    stroke: {
        dashArray: 4
    },
    colors: ["#ff6c2f", "#22c55e"],
    series: [parseFloat(completionRate)],
    labels: ['Completion Rate'],
    responsive: [{
        breakpoint: 380,
        options: {
            chart: {
                height: 180
            }
        }
    }],
    grid: {
        padding: {
            top: 0,
            right: 0,
            bottom: 0,
            left: 0
        }
    }
}

var chart = new ApexCharts(
    document.querySelector("#conversions"),
    options
);

chart.render();

// Providers Performance — Gross Bookings vs Net Revenue
// Providers — Net Revenue (SAR) • Line Chart

// const providers = [
//   "Lulu Beauty Lounge",
//   "Palm Oasis Salon",
//   "Yoush Beauty Salon",
//   "Riyadh Glam Studio",
//   "Jeddah Glow Spa",
//   "Dammam Beauty Hub",
//   "Al Khobar Chic Salon",
//   "Mecca Blossom Spa",
//   "Medina Pearl Beauty",
//   "Tabuk Luxe Lounge",
// ];

// const netRevenueSAR = [129500, 114000, 105800, 97800, 89300, 84500, 79200, 74400, 70800, 62800];

// function formatSAR(v){
//   if (v >= 1_000_000) return "SAR " + (v/1_000_000).toFixed(1) + "M";
//   if (v >= 1_000)    return "SAR " + (v/1_000).toFixed(1) + "k";
//   return "SAR " + v;
// }

// var options = {
//   series: [{
//     name: "Net Revenue",
//     data: netRevenueSAR
//   }],
//   chart: {
//     type: "line",
//     height: 360,
//     toolbar: { show: false }
//   },
//   title: {
//     text: "Providers — Net Revenue (SAR)",
//     align: "left",
//     margin: 10,
//     offsetY: 6,
//     style: { fontSize: "14px", fontWeight: 600 }
//   },
//   stroke: {
//     curve: "smooth",
//     width: 3
//   },
//   markers: {
//     size: 5,
//     strokeWidth: 2,
//     hover: { size: 7 }
//   },
//   dataLabels: {
//     enabled: true,
//     formatter: (val) => (val >= 1000 ? (val/1000).toFixed(1) + "k" : val),
//     offsetY: -8,
//     style: { fontSize: "11px" }
//   },
//   xaxis: {
//     categories: providers,
//     labels: {
//       rotate: -25,
//       trim: false,
//       style: { fontSize: "12px" }
//     },
//     axisTicks: { show: false },
//     axisBorder: { show: false }
//   },
//   yaxis: {
//     labels: {
//       formatter: (val) => formatSAR(val)
//     },
//     min: 0,
//     axisBorder: { show: false }
//   },
//   grid: {
//     show: true,
//     strokeDashArray: 3,
//     yaxis: { lines: { show: true } },
//     xaxis: { lines: { show: false } },
//     padding: { left: 10, right: 0 }
//   },
//   colors: ["#22c55e"],
//   tooltip: {
//     shared: false,
//     y: { formatter: (y) => formatSAR(y) }
//   },
//   legend: { show: false }
// };

// var chart = new ApexCharts(document.querySelector("#providers-performance-chart"), options);
// chart.render();

// Horizontal Bar — Providers Net Revenue (SAR) - Using Real Data

var colors = ["#B47EBA"];

function formatSAR(v){
  if (v >= 1_000_000) return "SAR " + (v/1_000_000).toFixed(1) + "M";
  if (v >= 1_000)    return "SAR " + (v/1_000).toFixed(1) + "k";
  return "SAR " + v.toLocaleString();
}

// Use real data from API or fallback to empty arrays
const topProviders = dashboardData.topProviders || [];
const providerNames = topProviders.map(p => p.name || 'N/A');
const providerRevenue = topProviders.map(p => parseFloat(p.revenue || 0));

// Only render chart if we have data
if (providerNames.length > 0 && providerRevenue.length > 0) {
  var options = {
    chart: {
      height: 400,
      type: "bar",
      toolbar: { show: false }
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: "48%",
        borderRadius: 4,
        dataLabels: { position: "top" }
      }
    },
    dataLabels: {
      enabled: true,
      formatter: function (val) { return formatSAR(val); },
      offsetY: -14,
      style: { fontSize: "11px" }
    },
    series: [{ name: "Net Revenue", data: providerRevenue }],
    colors: colors,
    xaxis: {
      categories: providerNames,
      labels: { rotate: -25, trim: false },
      axisTicks: { show: false },
      axisBorder: { show: false }
    },
    yaxis: {
      title: { text: "Net Revenue (SAR)" },
      labels: {
        formatter: function (v) {
          if (v >= 1_000_000) return (v/1_000_000).toFixed(1) + "M";
          if (v >= 1_000)    return (v/1_000).toFixed(0) + "k";
          return v;
        }
      },
      max: providerRevenue.length > 0 ? Math.ceil(Math.max(...providerRevenue) * 1.15) : 100
    },
    tooltip: {
      y: { formatter: function (val) { return formatSAR(val); } }
    },
    grid: { borderColor: "#f1f3fa" },
    states: { hover: { filter: "none" } }
  };

  var chart = new ApexCharts(document.querySelector("#provider-performance-chart"), options);
  chart.render();
} else {
  // Show placeholder message if no data
  const chartEl = document.querySelector("#provider-performance-chart");
  if (chartEl) {
    chartEl.innerHTML = '<div class="text-center text-muted py-5"><iconify-icon icon="solar:chart-bold-duotone" class="fs-48 mb-3 d-block"></iconify-icon><p class="mb-0">No revenue data available yet</p><small>Provider revenue will appear here once bookings are completed</small></div>';
  }
}


//
//Performance-chart
//
var options = {
    series: [{
        name: "Page Views",
        type: "bar",
        data: [34, 65, 46, 68, 49, 61, 42, 44, 78, 52, 63, 67],
    },
        {
            name: "Clicks",
            type: "area",
            data: [8, 12, 7, 17, 21, 11, 5, 9, 7, 29, 12, 35],
        },
    ],
    chart: {
        height: 313,
        type: "line",
        toolbar: {
            show: false,
        },
    },
    stroke: {
        dashArray: [0, 0],
        width: [0, 2],
        curve: 'smooth'
    },
    fill: {
        opacity: [1, 1],
        type: ['solid', 'gradient'],
        gradient: {
            type: "vertical",
            inverseColors: false,
            opacityFrom: 0.5,
            opacityTo: 0,
            stops: [0, 90]
        },
    },
    markers: {
        size: [0, 0],
        strokeWidth: 2,
        hover: {
            size: 4,
        },
    },
    xaxis: {
        categories: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
        ],
        axisTicks: {
            show: false,
        },
        axisBorder: {
            show: false,
        },
    },
    yaxis: {
        min: 0,
        axisBorder: {
            show: false,
        }
    },
    grid: {
        show: true,
        strokeDashArray: 3,
        xaxis: {
            lines: {
                show: false,
            },
        },
        yaxis: {
            lines: {
                show: true,
            },
        },
        padding: {
            top: 0,
            right: -2,
            bottom: 0,
            left: 10,
        },
    },
    legend: {
        show: true,
        horizontalAlign: "center",
        offsetX: 0,
        offsetY: 5,
        markers: {
            width: 9,
            height: 9,
            radius: 6,
        },
        itemMargin: {
            horizontal: 10,
            vertical: 0,
        },
    },
    plotOptions: {
        bar: {
            columnWidth: "30%",
            barHeight: "70%",
            borderRadius: 3,
        },
    },
    colors: ["#ff6c2f", "#22c55e"],
    tooltip: {
        shared: true,
        y: [{
            formatter: function (y) {
                if (typeof y !== "undefined") {
                    return y.toFixed(1) + "k";
                }
                return y;
            },
        },
            {
                formatter: function (y) {
                    if (typeof y !== "undefined") {
                        return y.toFixed(1) + "k";
                    }
                    return y;
                },
            },
        ],
    },
}

// Only render if element exists
const performanceChartEl = document.querySelector("#dash-performance-chart");
if (performanceChartEl) {
    var chart = new ApexCharts(performanceChartEl, options);
    chart.render();
}

class KsaVectorMap {
  initWorldMapMarker() {
    // Check if element exists
    const mapElement = document.querySelector('#ksa-map-markers');
    if (!mapElement) {
      console.log('KSA map element not found, skipping initialization');
      return;
    }

    // Get regions data from API or use default KSA cities
    const providersByRegion = dashboardData.providersByRegion || [];
    
    // Default KSA cities with coordinates
    const defaultCities = [
      { name: "Riyadh", coords: [24.7136, 46.6753], count: 0 },
      { name: "Jeddah", coords: [21.4858, 39.1925], count: 0 },
      { name: "Makkah", coords: [21.3891, 39.8579], count: 0 },
      { name: "Madinah", coords: [24.5247, 39.5692], count: 0 },
      { name: "Dammam", coords: [26.3927, 49.9777], count: 0 },
      { name: "Khobar", coords: [26.2172, 50.1971], count: 0 },
      { name: "Dhahran", coords: [26.2361, 50.0393], count: 0 },
      { name: "Tabuk", coords: [28.3838, 36.5662], count: 0 },
      { name: "Abha", coords: [18.2465, 42.5117], count: 0 },
      { name: "Jizan", coords: [16.8892, 42.5706], count: 0 },
      { name: "Hail", coords: [27.5114, 41.7208], count: 0 },
      { name: "Buraidah", coords: [26.3594, 43.9800], count: 0 },
    ];

    // Merge API data with default cities
    let saudiCities = defaultCities;
    
    if (providersByRegion.length > 0) {
      saudiCities = providersByRegion.map(region => {
        // Find matching default city for coordinates
        const defaultCity = defaultCities.find(c => 
          c.name.toLowerCase() === region.city.toLowerCase() ||
          c.name.toLowerCase().includes(region.city.toLowerCase()) ||
          region.city.toLowerCase().includes(c.name.toLowerCase())
        );
        
        return {
          name: `${region.city} (${region.count || 0})`,
          coords: defaultCity ? defaultCity.coords : region.coords || [24.7136, 46.6753],
          count: region.count || 0
        };
      });
    }

    const map = new jsVectorMap({
      map: 'world',
      selector: '#ksa-map-markers',
      zoomOnScroll: true,
      zoomButtons: true,
      markersSelectable: true,
      markers: saudiCities,
      markerStyle: {
        initial: { fill: "#7f56da" },
        selected: { fill: "#22c55e" },
      },
      labels: {
        markers: { render: marker => marker.name }
      },
      regionStyle: {
        initial: {
          fill: 'rgba(169,183,197,0.3)',
          fillOpacity: 1,
        },
      },
    });

    // Focus on Saudi Arabia
    if (map.setFocus) {
      try {
        map.setFocus({ regions: ['SA'], animate: true, scale: 6 });
      } catch (e) {
        // fallback: approximate center of KSA on the world projection
        map.setFocus({ x: 0.62, y: 0.47, scale: 6, animate: true });
      }
    }
  }

  init() {
    this.initWorldMapMarker();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new KsaVectorMap().init();
});

class VectorMap {


    initWorldMapMarker() {
        // Check if element exists
        const mapElement = document.querySelector('#world-map-markers');
        if (!mapElement) {
            console.log('World map element not found, skipping initialization');
            return;
        }

        const map = new jsVectorMap({
            map: 'world',
            selector: '#world-map-markers',
            zoomOnScroll: true,
            zoomButtons: false,
            markersSelectable: true,
            markers: [
                {name: "Canada", coords: [56.1304, -106.3468]},
                {name: "Brazil", coords: [-14.2350, -51.9253]},
                {name: "Russia", coords: [61, 105]},
                {name: "China", coords: [35.8617, 104.1954]},
                {name: "United States", coords: [37.0902, -95.7129]}
            ],
            markerStyle: {
                initial: {fill: "#7f56da"},
                selected: {fill: "#22c55e"}
            },
            labels: {
                markers: {
                    render: marker => marker.name
                }
            },
            regionStyle: {
                initial: {
                    fill: 'rgba(169,183,197, 0.3)',
                    fillOpacity: 1,
                },
            },
        });
    }

    init() {
        this.initWorldMapMarker();
    }

}

document.addEventListener('DOMContentLoaded', function (e) {
    new VectorMap().init();
});

// Client of Month Chart - Top Client Spending
const topClient = dashboardData.topClient || {};
const clientSpending = topClient.spending || [];

if (clientSpending.length > 0) {
  const options = {
    chart: { type: "bar", height: 280 },
    series: [{ name: "Spend", data: clientSpending.map(s => s.amount || 0) }],
    xaxis: { 
      categories: clientSpending.map(s => s.service || 'N/A'), 
      labels: { rotate: -25 } 
    },
    yaxis: { 
      title: { text: "Amount (SAR)" }, 
      labels: { formatter: v => `${v}` } 
    },
    plotOptions: { bar: { columnWidth: "45%", borderRadius: 6 } },
    dataLabels: { enabled: false },
    stroke: { width: 2 },
    colors: ["#D5B7D8"],
    tooltip: { 
      y: { 
        formatter: v => `SAR ${v.toLocaleString()}`, 
        title: { formatter: () => "" } 
      } 
    },
  };

  const el = document.querySelector("#clientofmonth");
  if (el) {
    const chart = new ApexCharts(el, options);
    chart.render();
  }
} else {
  // Show placeholder if no client data
  const el = document.querySelector("#clientofmonth");
  if (el) {
    el.innerHTML = '<div class="text-center text-muted py-3"><p class="small">No spending data available</p></div>';
  }
}




