
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

Vue.component('account-receivable-form',{
    props: ['taxpayer'],
    data() {
        return {
            id:0,
            taxpayer_id:'',
            chart_id:'',
            date:'',
            transaction_id:'',
            currency_id:'',
            rate:'',
            debit:'',
            credit:'',
            currencies:[],
            charts:[]

        }
    },


    methods: {


        //Takes Json and uploads it into Sales INvoice API for inserting. Since this is a new, it should directly insert without checking.
        //For updates code will be different and should use the ID's palced int he Json.
        onSave: function(json)
        {

            var app = this;
            var api = null;

            $.ajax({
                url: '',
                headers: {'X-CSRF-TOKEN': CSRF_TOKEN},
                type: 'post',
                data:json,
                dataType: 'json',
                async: false,
                success: function(data)
                {
                    if (data=='ok')
                    {
                        app.reset();
                        app.init();
                    }
                    else
                    {
                        alert('Something Went Wrong...')
                    }


                },
                error: function(xhr, status, error)
                {
                    console.log(xhr.responseText);
                }
            });
        },

        onReset: function()
        {
            var app=this;



            app.id=0;
            app.taxpayer_id= null;
            app.chart_id= null;
            app.date= null;
            app.transaction_id= null;
            app.currency_id= null;
            app.rate= null;
            app.debit= null;
            app.credit= null;


        },

        cancel()
        {
            var app=this;
            app.$parent.status=0;
        },
        getCurrencies: function(data)
        {
            var app=this;
            $.ajax({
                url: '/api/get_currency/' + this.taxpayer ,
                type: 'get',
                dataType: 'json',
                async: true,
                success: function(data)
                {
                    app.currencies=[];
                    for(let i = 0; i < data.length; i++)
                    {
                        app.currencies.push({name:data[i]['name'],id:data[i]['id']});
                    }

                },
                error: function(xhr, status, error)
                {
                    console.log(xhr.responseText);
                }
            });
        },
        getCharts: function(data)
        {
            var app=this;
            $.ajax({
                url: '/api/get_account/' + this.taxpayer ,
                type: 'get',
                dataType: 'json',
                async: true,
                success: function(data)
                {
                    app.charts = [];
                    for(let i = 0; i < data.length; i++)
                    {
                        app.charts.push({name:data[i]['name'],id:data[i]['id']});
                    }

                },
                error: function(xhr, status, error)
                {
                    console.log(xhr.responseText);
                }
            });
        }

    },

    mounted: function mounted()
    {
        this.getCurrencies();
    }
});
