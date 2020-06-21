<template>
    <div :class="containerClass" :id="'datagrid_' + _uid">
        <table class="table">
            <thead>
                <tr >
                    <th scope="col" v-for="header in headers">
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
                    <td v-for="(header, index) in headers">
                        <template v-if="header.hasFilter">
                            <template v-if="header.type == 'Select'">
                                <select :name="header.column" v-model="header.value" class="form-control" @change="filter">
                                    <template v-for="option in header.options">
                                        <option :value="option.value">
                                            {{option.label}}
                                        </option>
                                    </template>
                                </select>
                            </template>
                            <template v-else-if="header.type == 'DateTime'">
                                <div class="input-group">
                                    <input class="js-flatpickr form-control bg-white" :name="header.column" v-model="header.value" :data-header-index="index"
                                           readonly="readonly" :id="header.column + '-datetime'" @change="filter" />
                                    <button type="button" class="btn btn-sm btn-secondary" @click="clearDate(header.column + '-datetime')">
                                        <i class="fa fa-remove"></i>
                                    </button>
                                </div>
                            </template>
                            <template v-else-if="header.type == 'Button'"></template>
                            <template v-else>
                                <input type="text" :name="header.column" v-model="header.value" class="form-control" @keyup.enter="filter" />
                            </template>
                        </template>
                    </td>
                </tr>
            </thead>
            <tbody>
                <template v-if="rows.length">
                <tr v-for="row in rows">
                    <td v-for="column in row">
                        <template v-if="column.type == 'Button'">
                            <button class="btn" v-for="button in column.buttons" :class="button.class" @click="buttonClick(button.onclick)">
                                <i :class="button.icon_class"></i>
                                {{button.label}}
                            </button>
                        </template>
                        <template v-else-if="column.type == 'Icon'">
                            <template v-if="column.image != ''">
                                <img :src="column.image" class="icon" />
                            </template>
                        </template>
                        <template v-else>
                            <span v-html="column.value"></span>
                        </template>
                    </td>
                </tr>
                </template>
                <template v-else>
                    <tr>
                        <td :colspan="headers.length" class="text-center">
                            <template v-if="!loading">
                                No results found
                            </template>
                            <template v-else>
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
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
                                First
                            </button>
                        </li>
                        <li class="page-item" :class="page == 1 ? 'disabled' : ''">
                            <button type="button" class="page-link" :aria-disabled="page == 1" @click="changePage(page - 1 == 0 ? 1 : page - 1)">
                                Previous
                            </button>
                        </li>
                        <li class="page-item" v-for="paginationPage in paginationPages" :class="page == paginationPage ? 'active' : ''">
                            <button type="button" class="page-link" :aria-current="page == paginationPage ? 'page' : ''"
                            @click="changePage(paginationPage)">
                                {{paginationPage}}
                            </button>
                        </li>
                        <li class="page-item" :class="page == lastPage ? 'disabled' : ''">
                            <button type="button" class="page-link" :aria-disabled="page == lastPage" @click="changePage(page + 1 > lastPage ? lastPage : page + 1)">
                                Next
                            </button>
                        </li>
                        <li class="page-item" :class="page == lastPage ? 'disabled' : ''">
                            <button type="button" class="page-link" :aria-disabled="page == lastPage" @click="changePage(lastPage)">
                                Last ({{lastPage}})
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
                        <option v-for="allowed in allowedPerPage" :value="allowed" :selected="perPage == allowed">{{allowed}}</option>
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
            }
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
            }
        },
        methods: {
            filter() {
                this.sort = '';
                this.direction = '';

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
            clearDate(id) {
                $('#' + id).flatpickr().clear();
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
                    self.headers = response.data.headers;
                    self.rows = response.data.rows;
                    self.sort = response.data.sort;
                    self.direction = response.data.direction;
                    self.lastPage = response.data.last_page;
                    self.page = response.data.current_page;
                    self.allowedPerPage = response.data.allowedPerPage;
                    self.perPage = response.data.per_page;
                    self.setPaginationPages();

                }).catch(error => {

                }).finally(() => {
                    this.loading = false;
                });
            },
            buttonClick(fnc) {
                eval('this.' + fnc);
            },
            goto(url) {
                window.location = url;
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
            }
        },
        async mounted() {
            await this.fetch();
            let self = this;

            // Init Flatpickr (with .js-flatpickr class)
            jQuery('.js-flatpickr:not(.js-flatpickr-enabled)').each((index, element) => {
                let el = jQuery(element);

                // Add .js-flatpickr-enabled class to tag it as activated
                el.addClass('js-flatpickr-enabled');

                // Init it
                flatpickr(el, {});
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
