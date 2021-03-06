<b-table :data="list" hoverable :loading="isLoading">
    <template slot-scope="props">
        <b-table-column label="@lang('commercial.Supplier')" sortable>
            @{{ props.row.Supplier }}
        </b-table-column>

        <b-table-column field="Number" label="@lang('commercial.InvoiceNumber')" sortable>
            @{{ props.row.Number }}
        </b-table-column>
        <b-table-column field="Expiry" numeric label="@lang('global.Deadline')" sortable>
            @{{ new Date(props.row.Expiry).toLocaleDateString() }}
        </b-table-column>
        <b-table-column field="Currency">
            <span class="m-badge m-badge--success m-badge--wide m-badge--rounded">
                @{{ props.row.Currency }}
            </span>
        </b-table-column>
        <b-table-column field="Value" numeric label="@lang('global.Total')" sortable>
            @{{ new Number(props.row.Value).toLocaleString() }}
        </b-table-column>
        <b-table-column field="Balance" numeric label="@lang('global.Balance')" sortable>
            @{{ new Number(props.row.Balance).toLocaleString() }}
        </b-table-column>

        <b-table-column custom-key="actions" numeric>
            <a href="#" v-on:click="onEdit(props.row.id)" class="btn btn-outline-primary btn-sm m-btn m-btn--icon">
                <i class="la la-money"></i> @lang('commercial.MakePayment')
            </a>
        </b-table-column>
    </template>
</b-table>

<b-pagination :total="meta.total" :current.sync="meta.current_page" :simple="false" :per-page="meta.per_page" @change="pageChange"> </b-pagination>
