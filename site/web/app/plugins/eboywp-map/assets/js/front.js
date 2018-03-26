var EWP_MAP = EWP_MAP || {};

(function($) {

    EWP_MAP.markersArray = [];
    EWP_MAP.activeMarker = null;
    EWP_MAP.is_filtering = false;

    wp.hooks.addAction('eboywp/refresh/map', function($this, facet_name) {
        var selected_values = [];

        if (EWP_MAP.is_filtering) {
            selected_values = EWP_MAP.map.getBounds().toUrlValue().split(',');
        }

        EWP.facets[facet_name] = selected_values;
        EWP.frozen_facets[facet_name] = 'hard';
    });

    wp.hooks.addAction('eboywp/reset', function() {
        $.each(EWP.facet_type, function(name, type) {
            if ('map' === type) {
                EWP.frozen_facets[name] = 'hard';
            }
        });
    });

    wp.hooks.addFilter('eboywp/selections/map', function(label, params) {
        return EWP_JSON['map']['resetText'];
    });

    function do_refresh() {
        if (EWP_MAP.is_filtering) {
            EWP.autoload();
        }
    }

    $(document).on('click', '.eboywp-map-filtering', function() {
        var $this = $(this);

        if ($this.hasClass('enabled')) {
            $this.text(EWP_JSON['map']['filterText']);
            EWP_MAP.is_filtering = false;
            EWP.autoload();
        }
        else {
            $this.text(EWP_JSON['map']['resetText']);
            EWP_MAP.is_filtering = true;
        }

        $this.toggleClass('enabled');
    });

    $(document).on('eboywp-loaded', function() {
        if ('undefined' === typeof EWP.settings.map || '' === EWP.settings.map) {
            return;
        }

        if (! EWP.loaded) {

            EWP_MAP.map = new google.maps.Map(document.getElementById('eboywp-map'), EWP.settings.map.init);

            EWP_MAP.map.addListener('dragend', function() {
                do_refresh();
            });

            EWP_MAP.map.addListener('zoom_changed', function() {
                do_refresh();
            });

            google.maps.event.addDomListener(window, 'resize', function() {
                var center = EWP_MAP.map.getCenter();
                google.maps.event.trigger(EWP_MAP.map, 'resize');
                EWP_MAP.map.setCenter(center);
            });

            EWP_MAP.oms = new OverlappingMarkerSpiderfier(EWP_MAP.map, {
                markersWontMove: true,
                markersWontHide: true,
                basicFormatEvents: true
            });
        }
        else {
            clearOverlays();
        }

        // this needs to re-init on each refresh
        EWP_MAP.bounds = new google.maps.LatLngBounds();

        $.each(EWP.settings.map.locations, function(idx, obj) {
            var args = $.extend({
                map: EWP_MAP.map,
                position: obj.position,
                info: new google.maps.InfoWindow({
                    content: obj.content
                })
            }, obj);

            var marker = new google.maps.Marker(args);

            google.maps.event.addListener(marker, 'spider_click', function() {
                if (null !== EWP_MAP.activeMarker) {
                    EWP_MAP.activeMarker.info.close();
                }

                marker.info.open(EWP_MAP.map, marker);
                EWP_MAP.activeMarker = marker;
            });

            EWP_MAP.oms.addMarker(marker);
            EWP_MAP.markersArray.push(marker);
            EWP_MAP.bounds.extend(marker.getPosition());
        });

        var config = EWP.settings.map.config;

        if ('yes' === config.cluster) {
            EWP_MAP.mc = new MarkerClusterer(EWP_MAP.map, EWP_MAP.markersArray, {
                imagePath: EWP.settings.map.imagePath,
                imageExtension: EWP.settings.map.imageExtension,
                maxZoom: 14
            });
        }

        if (! EWP_MAP.is_filtering && 0 < EWP.settings.map.locations.length) {
            EWP_MAP.map.fitBounds(EWP_MAP.bounds);
        }
        else if (0 < config.default_lat && 0 < config.default_lng) {
            EWP_MAP.map.setCenter({
                lat: parseFloat(config.default_lat),
                lng: parseFloat(config.default_lng)
            });
        }
    });

    // Clear markers
    function clearOverlays() {
        EWP_MAP.oms.removeAllMarkers();
        EWP_MAP.markersArray = [];

        // clear clusters
        if ('undefined' !== typeof EWP_MAP.mc) {
            EWP_MAP.mc.clearMarkers();
        }
    }

})(jQuery);
