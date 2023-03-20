<template>
    <div :class="containerClass" :id="'datagrid_' + _uid">
        <div v-if="gridActions.length" class="input-group mb-3 w-25">
            <div class="input-group-prepend">
                <label class="input-group-text" for="inputGridActionSelect">Action</label>
            </div>
            <select class="custom-select" id="inputGridActionSelect" @change="gridActionChange($event)" :disabled="!selected.length">
                <option selected></option>
                <option v-for="(gridAction, index) in gridActions" :value="gridAction.value" :key="index">
                    {{ gridAction.label }}
                </option>
            </select>
        </div>
        <table class="table">
            <thead>
                <tr >
                    <th scope="col" v-for="header in headers" :key="header.column + '_name'">
                        <template v-if="header.hasSort">
                            <span @click="resort(header.column)" class="clickable">
                                {{header.label}}
                                <i v-if="sort == header.column && direction == 'asc'" class="fa fa-sort-down"></i>
                                <i v-else-if="sort == header.column && direction == 'desc'" class="fa fa-sort-up"></i>
                                <i v-else class="fa fa-sort"></i>
                            </span>
                        </template>
                        <template v-else>
                            {{header.label}}
                        </template>
                    </th>
                </tr>
                <tr>
                    <td v-for="(header, index) in headers" :key="header.column + '_field'">
                        <template v-if="header.hasFilter">
                            <template v-if="header.type == 'Select'">
                                <select :name="header.column" v-model="header.value" class="form-control" @change="filter">
                                    <template v-for="option in header.options">
                                        <option :value="option.value" :key="'header_' + header.column + '_' + option.value">
                                            {{option.label}}
                                        </option>
                                    </template>
                                </select>
                            </template>
                            <template v-else-if="header.type == 'DateTime'">
                                <div class="input-group">
                                    <div class="d-none" :id="'poDate_' + header.column">
                                        <div class="popover_date">
                                            <div class="form-group row">
                                                <label :for="header.column + '_from'" class="col-form-label ml-2">From</label>
                                                <div class="col-sm-10">
                                                    <div class="input-group">
                                                        <input type="date" class=" form-control" :name="header.column + '_from'" :id="header.column + '-from'"
                                                            :data-header-index="index + '_from'" :value="getDate(header.value)" />
                                                        <div class="input-group-prepend">
                                                            <button type="button" class="btn btn-secondary btn-sm" @click="clearValue(header, '-from')">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label :for="header.column + '_till'" class="col-form-label ml-3">Till</label>
                                                <div class="col-sm-10">
                                                    <div class="input-group">
                                                        <input type="date" class="form-control" :name="header.column + '_till'" :id="header.column + '-till'"
                                                            :data-header-index="index + '_till'" :value="getDate(header.value, true)" />
                                                        <div class="input-group-prepend">
                                                            <button type="button" class="btn btn-secondary btn-sm" @click="clearValue(header, '-till')">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row d-block m-2 position-relative">
                                                <button type="button" class="btn btn-secondary" @click="hideDate(header.column + '-date')">
                                                    Cancel
                                                </button>
                                                <button type="button" class="btn btn-primary float-right" @click="applyDate(header)">
                                                    Apply
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group">
                                        <input class="form-control bg-white" :name="header.column" v-model="header.value" readonly="readonly" data-placement="bottom"
                                            :id="header.column + '-date'" data-toggle="popover" data-container="body" :data-target="'#poDate_' + header.column" />
                                        <div class="input-group-prepend">
                                            <button type="button" class="btn btn-secondary btn-sm" @click="clearValue(header)">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template v-else-if="header.type == 'Button'"></template>
                            <template v-else-if="header.type == 'Checkbox'">
                                <input type="checkbox" v-model="selectAll">
                            </template>
                            <template v-else>     
                                <input type="text" :name="header.column" v-model="header.value" class="form-control" @keyup.enter="filter" />
                            </template>
                        </template>
                    </td>
                </tr>
            </thead>
            <tbody>
                <template v-if="rows.length">
                <tr v-for="(row, index) in rows" :key="'r_' + index">
                    <td v-for="(column, index) in row" :key="'c_' + index">
                        <template v-if="column.type == 'Button'">
                            <template v-if="column.buttonGroup">
                                <div class="btn-group">
                                    <template  v-for="(button, index) in column.buttons">
                                        <template v-if="button.isLink">
                                            <a class="btn" :class="button.class" v-bind:href="button.onclick" :disabled="button.disabled"
                                                :title="button.title != undefined ? button.title :''" :name="button.name != undefined ? button.name : ''" 
                                                :key="'btn_' + column.name + '_' + index">
                                                <i :class="button.icon_class"></i>
                                            </a>
                                        </template>
                                        <template v-else>
                                            <button class="btn" :class="button.class" @click="buttonClick(button.onclick, button.args, $event)" :disabled="button.disabled"
                                                    :title="button.title != undefined ? button.title :''" :name="button.name != undefined ? button.name : ''"
                                                    :key="'btn_' + column.name + '_' + index">
                                                <i :class="button.icon_class"></i>
                                                {{button.label}}d
                                            </button>
                                        </template>
                                    </template>
                                </div>
                            </template>
                            <template v-else>
                                <template  v-for="(button, index) in column.buttons">
                                    <template v-if="button.isLink">
                                        <a class="btn" :class="button.class" v-bind:href="button.onclick" :disabled="button.disabled"
                                           :title="button.title != undefined ? button.title :''" :name="button.name != undefined ? button.name : ''"
                                           :key="'btn_' + column.name + '_' + index">
                                            <i :class="button.icon_class"></i>
                                        </a>
                                    </template>
                                    <template v-else>
                                        <button class="btn" :class="button.class" @click="buttonClick(button.onclick, button.args, $event)" :disabled="button.disabled"
                                                :title="button.title != undefined ? button.title :''" :name="button.name != undefined ? button.name : ''"
                                                :key="'btn_' + column.name + '_' + index">
                                            <i :class="button.icon_class"></i>
                                            {{button.label}}
                                        </button>
                                    </template>
                                </template>
                            </template>
                        </template>
                        <template v-else-if="column.type == 'Icon'">
                            <template v-if="column.image != ''">
                                <img :src="column.image" class="icon" />
                            </template>
                        </template>
                        <template v-else-if="column.type == 'Checkbox'">
                            <input :id="'cb_' + column.name + '_' + index" type="checkbox" v-model="selected" :value="column.value">
                        </template>
                        <template v-else>
                            <span v-html="column.value" :class="column.class"></span>
                        </template>
                    </td>
                </tr>
                </template>
                <template v-else>
                    <tr>
                        <td :colspan="headers.length" class="text-center">
                            <template v-if="!loading">
                                {{noResultsText}}
                            </template>
                            <template v-else>
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">{{loadingText}}</span>
                                </div>
                            </template>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <div class="row" v-if="paginationPages.length > 1">
            <div class="col-6">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item" :class="page == 1 ? 'disabled' : ''">
                            <button type="button" class="page-link" :aria-disabled="page == 1" @click="changePage(1)">
                                {{firstText}}
                            </button>
                        </li>
                        <li class="page-item" :class="page == 1 ? 'disabled' : ''">
                            <button type="button" class="page-link" :aria-disabled="page == 1" @click="changePage(page - 1 == 0 ? 1 : page - 1)">
                                {{previousText}}
                            </button>
                        </li>
                        <li class="page-item" v-for="(paginationPage, index) in paginationPages" :class="page == paginationPage ? 'active' : ''" :key="'pag_' + index">
                            <button type="button" class="page-link" :aria-current="page == paginationPage ? 'page' : ''"
                            @click="changePage(paginationPage)">
                                {{paginationPage}}
                            </button>
                        </li>
                        <li class="page-item" :class="page == lastPage ? 'disabled' : ''">
                            <button type="button" class="page-link" :aria-disabled="page == lastPage" @click="changePage(page + 1 > lastPage ? lastPage : page + 1)">
                                {{nextText}}
                            </button>
                        </li>
                        <li class="page-item" :class="page == lastPage ? 'disabled' : ''">
                            <button type="button" class="page-link" :aria-disabled="page == lastPage" @click="changePage(lastPage)">
                                {{lastText}} ({{lastPage}})
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="col-6 text-right align-middle">
                <div class="input-group">
                    <span class="goto-prepend"></span>
                    <span class="amount-perpage mr-2">Amount per page</span>
                    <select v-if="allowedPerPage.length > 1" class="mr-4 form-control amount-perpage-select" @change="changeAmountPerPage">
                        <option v-for="(allowed, index) in allowedPerPage" :value="allowed" :selected="perPage == allowed" :key="'pag_allowed_' + index">
                            {{allowed}}
                        </option>
                    </select>
                    <input type="number" name="page" id="goto-page" class="form-control goto-page" v-on:keyup.enter="changePageCustom">
                    <button type="button" class="btn btn-primary input-group-append" @click="changePageCustom">Go to page</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "DataGrid",
        props: {
            url: {
                type: String,
                default: ''
            },
            containerClass: {
                type: String,
                default: 'data-grid-container'
            },
            noResultsText: {
                type: String,
                default: 'No results found'
            },
            loadingText: {
                type: String,
                default: 'Loading...'
            },
            firstText: {
                type: String,
                default: 'First'
            },
            previousText: {
                type: String,
                default: 'Previous'
            },
            nextText: {
                type: String,
                default: 'Next'
            },
            lastText: {
                type: String,
                default: 'Last'
            },
        },
        data() {
            return {
                headers: [],
                rows: [],
                loading: true,
                sort: '',
                direction: '',
                page: 1,
                paginationPages: [],
                lastPage: 1,
                visiblePages: 5,
                allowedPerPage: [],
                perPage: 10,
                selectedValues: [],
                selected: [],
                gridActions: [],
            }
        },
        methods: {
            filter() {
                this.sort = '';
                this.direction = '';
                this.page = 1;
                
                this.fetch();
            },
            resort(column) {
                if (this.sort == column) {
                    this.direction = (this.direction == 'asc' ? 'desc' : 'asc');
                } else {
                    this.sort = column;
                    this.direction = '';
                }
                this.fetch();
            },
            clearValue(header, id) {
                if (id != undefined) {
                    $('#' + header.column + id).val('');
                    return;
                }

                header.value = '';
                this.filter();
            },
            changePageCustom() {
                let page = $('#goto-page').val();
                if (page != '' && !isNaN(page)) {
                    page = parseInt(page);
                    if (page < 1) {
                        page = 1;
                    }

                    if (page > this.lastPage) {
                        page = this.lastPage;
                    }
                    $('#goto-page').val(page);

                    this.changePage(page);
                }
            },
            changePage(page)
            {
                if (page < 1) {
                    page = 1;
                }

                if (page > this.lastPage) {
                    page = this.lastPage;
                }

                if (this.page == page) {
                    return;
                }

                this.fetch(page);
            },
            changeAmountPerPage() {
                if (this.allowedPerPage.indexOf(parseInt(event.target.value)) != -1) {
                    this.perPage = event.target.value;
                    this.fetch(1);
                }
            },
            fetch(page) {
                this.loading = true;
                let self = this;
                let qs = require('qs');
                let data = {
                    headers: this.headers,
                    sort: this.sort,
                    direction: this.direction,
                    page: page === undefined ? this.page : page,
                    perPage: this.perPage,
                };

                return axios.post(this.url, qs.parse(data)).then(response => {
                    if (self.headers.length == 0) {
                        self.headers = response.data.headers;
                    }
                    self.rows = response.data.rows;
                    self.sort = response.data.sort;
                    self.direction = response.data.direction;
                    self.lastPage = response.data.last_page;
                    self.page = response.data.current_page;
                    self.allowedPerPage = response.data.allowedPerPage;
                    self.perPage = response.data.per_page;
                    self.gridActions = response.data.gridActions;
                    self.setPaginationPages();
                }).catch(error => {

                }).finally(() => {
                  this.loading = false;
                });
            },
            delete(data) {
                this.loading = true;
                axios.delete(data.url).then(response => {
                    let type = 'success';
                    if (response.data.error) {
                        type = 'error';
                    }

                    if (response.data.message) {
                        Vue.$toast.open({
                            type: type,
                            message: response.data.message
                        });
                    }
                }).catch(error => {

                }).finally(() => {
                    this.loading = false;
                });
            },
            buttonClick(fnc, args, event) {
                try {
                    this.$emit(fnc, { args: args, event: event});
                } catch (e) {}

                try {
                    eval('this.' + fnc);
                } catch (e) {}
            },
            goto(url) {
                window.location = url;
            },
            setPaginationPages() {
                let paginationPages = [];

                let startPage = 1;

                let pages = this.visiblePages > this.lastPage ? this.lastPage : this.visiblePages;
                let centerPagePosition = Math.max(Math.floor(pages / 2), 1);
                if (this.page > centerPagePosition) {
                    startPage = this.page - centerPagePosition;
                }

                if (startPage + pages > this.lastPage) {
                    startPage = this.lastPage - pages + 1;
                }

                for (let i = 0; i < pages; i++) {
                    paginationPages.push(startPage + i);
                }
                this.paginationPages = paginationPages;
            },
            getDate(date, isTill) {
                let dates = date.split(' ');
                if (isTill) {
                    if (date.indexOf('till') != -1) {
                        return (dates[dates.length - 1] != undefined ? dates[dates.length - 1] : '');
                    }
                    return '';
                }

                return (dates[0] != undefined ? dates[0] : '');
            },
            applyDate(header) {
                let from = $('#' + header.column + '-from').val();
                let till = $('#' + header.column + '-till').val();
                
                header.value = from + (till != '' ? ' till ' + till : '');
                $('#' + header.column + '-date').popover('hide');
                
                this.filter();
            },
            hideDate(elementId) {
                $('#' + elementId).popover('hide');
            },
            getPageCheckboxValues(){
                let pageCheckboxValues = [];
                this.rows.forEach(function(row) {
                    row.forEach(function(column) {
                        if (column.type === "Checkbox") {
                            pageCheckboxValues.push(column.value);
                        }
                    });
                });
                return pageCheckboxValues;
            },
            gridActionChange(event) {
                let selectedGridAction = event.target.value;
                if (selectedGridAction && this.selected.length) {
                    try {
                        this.$emit("gridActionUpdate", {action:selectedGridAction, ids:this.selected});
                    } catch (e) {
                    }
                }
                event.target.value = "";
            }
        },
        computed: {
            selectAll: {
                get() {
                    let selectedValues = this.selected;
                    let pageCheckboxValues = this.getPageCheckboxValues();
                    return pageCheckboxValues.every(val => selectedValues.includes(val));
                },
                set(isChecked) {
                    let pageCheckboxValues = this.getPageCheckboxValues();
                    if (isChecked) {
                        this.selected.push(...pageCheckboxValues);
                    } else {
                        this.selected = this.selected.filter(val => !pageCheckboxValues.includes(val));
                    }
                }
            }
        },
        async mounted() {
            await this.fetch();
            let self = this;

            $("[data-toggle='popover']").each(function(index, element) {
                var elementId = $(element).data().target;
                var content = $(elementId).removeClass('d-none');
                $(elementId).remove();
                $(element).popover({
                    content: content,
                    html: true
                });
            });

        }
    }
</script>

<style scoped>
    .icon {
        width: 64px;
        height: 64px;
    }
    .clickable:hover {
        cursor: pointer;
    }
    .goto-page {
        width: 85px;
        flex: none;
    }
    .goto-prepend {
        flex: 1 1 0%;
    }
    .amount-perpage {
        padding-top:8px;
    }
    .amount-perpage-select {
        width: 80px;
        flex:none;
    }
    td {
        vertical-align: middle;
    }
    td > .icon {
        border: 1px solid #888;
    }
    td > .datepicker {
        width:85px;
    }
</style>
<style>
    .popover_date > .form-group.row {
        padding: 0 10px;
    }
    .popover-body {
        background-color: #343a40;
        border: 1px solid #fff;
        border-radius: 4px;
        color:#fff;
    }
</style>
