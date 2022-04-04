function renderPlugin_snmppinfo(data) {

    var directives = {
        Device: {
            text: function () {
                var Name = (this.Name !== undefined) ? (' (' + this.Name + ')'): '';
                return this.Device + Name;
            }
        },
        Percent: {
            html: function () {
                var max = parseInt(this.MaxCapacity, 10);
                var level = parseInt(this.Level, 10);
                var percent = 0;

                if (max>0 && (level>=0) && (level<=max) ) {
                    percent = Math.round(100*level/max);
                } else if (max==-2 && (level>=0) && (level<=100) ) {
                    percent = level;
                } else if (level==-3) {
                    percent = 100;
                }
                return '<div class="progress"><div class="progress-bar progress-bar-info" style="width:' + percent + '%;"></div>' +
                        '</div><div class="percent">' + percent + '%</div>';
            }
        },
        Units: {
            html: function () {
                var max = parseInt(this.MaxCapacity, 10);
                var level = parseInt(this.Level, 10);

                if (max>0 && (level>=0) && (level<=max) ) {
                    return level+" / "+max;
                } else if (max==-2 && (level>=0) && (level<=100) ) {
                    return level+" / 100";
                } else if (level==-3) {
                    return genlang(5, 'snmppinfo'); // enough
                } else {
                    return genlang(6, 'snmppinfo'); // unknown
                }
            }
        },
        SUnits: {
            html: function () {
                var supply = parseInt(this.SupplyUnit, 10);
                if (isNaN(supply)) {
                    return "";
                } else {
                    switch (supply) {
                        case 7:
                            return "<br>" + genlang(9, "snmppinfo");
                        case 13:
                            return "<br>" + genlang(8, "snmppinfo");
                        case 15:
                            return "<br>" + genlang(7, "snmppinfo");
                        case 19:
                            return "<br>" + genlang(3, "snmppinfo");
                    }
                }
            }
        }
    };

    if (data.Plugins.Plugin_SNMPPInfo !== undefined) {
        var printers = items(data.Plugins.Plugin_SNMPPInfo.Printer);
        if (printers.length > 0) {
            var i, j, datas;
            var html = "";
            for (i = 0; i < printers.length; i++) {
                html+="<tr id=\"snmppinfo-" + i + "\" class=\"treegrid-snmppinfo-" + i + "\" style=\"display:none;\" >";
                html+="<td colspan=\"3\"><span class=\"treegrid-spanbold\" data-bind=\"Device\"></span></td>";
                html+="</tr>";

                try {
                    datas = items(printers[i].MarkerSupplies);
                    for (j = 0; j < datas.length; j++) {
                        html+="<tr id=\"snmppinfo-" + i + "-" + j +"\" class=\"treegrid-parent-snmppinfo-" + i + "\">";
                        html+="<td><span class=\"treegrid-spanbold\" data-bind=\"Description\"></span></td>";
                        html+="<td><span data-bind=\"Percent\"></span></td>";
                        html+="<td class=\"rightCell\"><span data-bind=\"Units\"></span><span data-bind=\"SUnits\"></span></td>";
                        html+="</tr>";
                   }
                }
                catch (err) {
                   $("#snmppinfo-" + i).hide();
                }
            }

            $("#snmppinfo-data").empty().append(html);

            for (i = 0; i < printers.length; i++) {
                $('#snmppinfo-'+ i).render(printers[i]["@attributes"], directives);
                try {
                    datas = items(printers[i].MarkerSupplies);
                    for (j = 0; j < datas.length; j++) {
                        $('#snmppinfo-'+ i+ "-" + j).render(datas[j]["@attributes"], directives);
                   }
                }
                catch (err) {
                   $("#snmppinfo-" + i).hide();
                }
            }

            $('#snmppinfo').treegrid({
                initialState: 'expanded',
                expanderExpandedClass: 'normalicon normalicon-down',
                expanderCollapsedClass: 'normalicon normalicon-right'
            });

            $('#block_snmppinfo').show();
        } else {
            $('#block_snmppinfo').hide();
        }
    } else {
        $('#block_snmppinfo').hide();
    }
}
