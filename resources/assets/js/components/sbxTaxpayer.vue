<template>
    <div>
        <div class="input-group m-input-group">

            <div v-if="selectText != ''" class="input-group-prepend">
                <span class="input-group-text m--font-boldest">
                    {{ selectText }}
                </span>
            </div>

            <input type="text" name ="taxpayer" class="form-control m-input" placeholder="Buscar" aria-describedby="basic-addon2" autocomplete="off"

            v-model="query"
            @keydown.down="down"
            @keydown.up="up"
            @keydown.enter="hit"
            @keydown.esc="reset"
            @blur="reset"
            @input="update"/>
            <div class="input-group-append">
                <span class="input-group-text" id="basic-addon1">
                    <i v-if="loading" class="fa fa-spinner fa-spin"></i>
                    <template v-else>
                        <i class="fa fa-search" v-show="isEmpty"></i>
                        <i class="fa fa-times" v-show="isDirty" @click="reset"></i>
                    </template>
                </span>
            </div>
        </div>
        <ul v-show="hasItems">
            <li v-for="(item, $item) in items" :class="activeClass($item)" @mousedown="hit" @mousemove="setActive($item)">
                <span class="name" v-text="item.name"></span>
                <span>|</span>
                <span class="screen-name" v-text="item.taxid"></span>
            </li>
        </ul>
    </div>
</template>

<script>

import VueTypeahead from 'vue-typeahead'
import Vue from 'vue'
import Axios from 'axios'

var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
Vue.prototype.$http = Axios

export default {
    extends: VueTypeahead,
    props: ['selectedTaxPayer', 'country', 'lblSearch', 'lblPleaseSelect'],
    data () {
        return {
            name:'',
            taxid:'',
            address:'',
            email:'',
            code:'',
            telephone:'',
            src: '/api/' + this.country + '/get_taxpayers/',
            limit: 15,
            minChars: 3,
            queryParamName: '',
            selectText:'Favor Elegir',
            id:'',
        }
    },

    methods:
    {
        onHit (item)
        {
            var app = this;
            app.$parent.taxid = item.taxid;
            app.$parent.name = item.name;
            app.$parent.alias = item.alias;
            app.$parent.id = item.id;
            app.$parent.email = item.email;
            app.$parent.telephone = item.telephone;
            app.$parent.address = item.address;

            $.ajax(
                {
                    url: '/api/' + this.country + '/get_owner/' + item.id,
                    headers: {'X-CSRF-TOKEN': CSRF_TOKEN},
                    type: 'get',
                    dataType: 'json',
                    async: false,
                    success: function(data)
                    {
                        if (data != null)
                        {
                            app.$parent.no_owner = 1;
                            app.$parent.owner_img = data.team.photo_url;
                            app.$parent.owner_name = data.team.name;
                            app.$parent.owner_type = data.type;
                        }
                        else
                        {
                            app.$parent.no_owner = 0;
                            app.$parent.owner_img = '';
                            app.$parent.owner_name = '';
                            app.$parent.owner_type = '';
                        }
                    },
                    error: function(xhr, status, error)
                    {
                        console.log(xhr.responseText);
                    }
                });
            }
        }
    }


    </script>

    <style scoped>

    .fa-times
    {
        cursor: pointer;
    }

    i
    {
        float: right;
        position: relative;
        opacity: 0.4;
    }

    ul
    {
        position: absolute;
        padding: 0;
        min-width: 100%;
        background-color: #fff;
        list-style: none;
        border-radius: 4px;
        box-shadow: 0 0 10px rgba(0,0,0, 0.25);
        z-index: 1000;
    }

    li
    {
        padding: 5px;
        border-bottom: 1px solid whitesmoke;
        cursor: pointer;
    }

    li:first-child
    {
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
    }

    li:last-child
    {
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
        border-bottom: 0;
    }

    span
    {
        color: #2c3e50;
    }

    .active
    {
        background-color: #734cea;
    }

    .active span
    {
        color: white;
    }

    .strong
    {
        font-weight: 800;
        font-style: italic;
    }
    </style>
