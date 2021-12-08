function initMap() {
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 12,
    // center: { lat: 37.06, lng: -95.68 },
  });

  if (mapdata) {
    const kmlLayer = new google.maps.KmlLayer({
      url: mapdata.kml,
      suppressInfoWindows: true,
      map: map,
    });

    // kmlLayer.addListener("click", (kmlEvent) => {
    //   // const text = kmlEvent.featureData.description;
    //   console.log(kmlEvent.featureData)
    // });
    // 
    kmlLayer.addListener("tilesloaded", (kmlEvent) => {
      // const text = kmlEvent.featureData.description;
      console.log(kmlEvent.featureData)
    });
    
    // kmlLayer.addListener(search_address);

    // kmlLayer.addEventListener('load', (event) => {
    //     event.featureData.map(search_address)
    //     // console.log('page is fully loaded');
        
    //     var BreakException = {};

    //     try {
    //       event.featureData.forEach(function(el) {
    //         console.log(el);
    //         if (el.name == "Melbourne CBD") throw BreakException;
    //       });
    //     } catch (e) {
    //       if (e !== BreakException) throw e;
    //     }
    // });
    

  }

}

function search_address(data) {
  if (data.name == "Melbourne CBD") {
    console.log('asd')
  }
}