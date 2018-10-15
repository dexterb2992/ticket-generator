<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">
                        A simple lottery ticket generator
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="card-title">Get your ticket now</div>
                                <button class="btn btn-lg btn-info btn-flat" @click="generateTicket" :disabled="generating">
                                    <i class="fa fa-random" v-show="!generating"></i>
                                    <i class="fa fa-spin fa-spinner" v-show="generating"></i>
                                    Generate
                                </button>
                            </div>

                            <div class="col">
                                <form @submit.prevent="submitTicket" v-show="numbers.length">
                                    <alert :notification="notification" v-if="notification.show"></alert>
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" v-model="name" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Ticket</label>
                                        <template v-for="(number, key) in numbers">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <!-- <button class="btn btn-outline-secondary" type="button"
                                                        @click="selectedKey = key" title="Select">
                                                        <i class="fa fa-check" v-if="selectedKey == key"></i>
                                                        <i class="fa fa-angle-right" v-else></i>
                                                    </button> -->
                                                </div>
                                                <input type="number" class="form-control" v-model="numbers[key]"
                                                    @click="selectedKey = key" @change="checkTickets"
                                                    min="1" max="99" step="1"
                                                    pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==2) return false;">

                                            </div>
                                        </template>
                                    </div>
                                    <button class="btn btn-success" :disabled="submitting">
                                        <i class="fa fa-send" v-show="!submitting"></i>
                                        <i class="fa fa-spinner fa-spin" v-show="submitting"></i>
                                        Submit
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    a.btn.btn-sm.btn-info {
        min-height: 32px;
        min-width: 32px;
    }
</style>

<script>
    import Alert from './Alert.vue';

    export default {
        components: {
            'alert': Alert
        },
        data() {
            return {
                generating: false,
                submitting: false,
                numbers: [],
                name: '',
                selectedKey: null,
                notification: {
                    type: 'info',
                    message: '',
                    show: false
                },
                ticket: ''
            }
        },
        mounted() {
            Event.listen('hideAlert', () => {
                this.notification.show = false;
                this.notification.message = '';
            })
        },
        methods: {
            pad (str, max) {
              str = str.toString();
              return str.length < max ? this.pad("0" + str, max) : str;
            },
            notify(messsage, type) {
                this.notification.show = true;
                this.notification.message = messsage;
                this.notification.type = type;
            },
            hideNotification() {
                this.notification.show = false;
                this.notification.message = '';
                this.notification.type = 'info';
            },
            generateTicket() {
                this.generating = true;

                axios.get('/api/generate').then(res => {
                    if (res.data.success) {
                        this.numbers = res.data.numbers

                    }
                }).catch(err => {
                    this.notify("Something went wrong, please try again later.", "danger");

                }).then(() => {
                    this.generating = false;
                });
            },

            submitTicket() {
                var data = {
                    name: this.name,
                    number: this.generateTicketString()
                };

                this.submitting = true;

                axios.post('/api/submit', data).then((res) => {
                    if (res.data.success == true) {
                        this.notify(res.data.message, "success");
                    } else {
                        this.notify(res.data.message, "danger");
                    }

                }).catch(err => {
                    if (err.response) {
                        this.notify(err.response.data.message, "danger");
                    }
                }).then(() => {
                    this.submitting = false;
                });
            },

            generateTicketString() {
                var str = "";
                for (var i = this.numbers.length - 1; i >= 0; i--) {
                    str += this.pad(this.numbers[i], 2);

                    if (i != 0) {
                        str+= '-';
                    }
                }

                return str;
            },

            checkTickets(e) {
                console.log(e);
                this.ticket = this.generateTicketString();

                axios.get(`/api/check?number=${this.ticket}`).then((res) => {
                    console.log(res.data);
                    if (!res.data.success) {
                        this.notify(res.data.message, "danger");
                    }
                });
            }
        }
    }
</script>
