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
                                        <label>Numbers</label>
                                        <template v-for="(number, key) in numbers">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        @click="selectedKey = key">
                                                        <i class="fa fa-check" v-if="selectedKey == key"></i>
                                                        <i class="fa fa-angle-right" v-else></i>
                                                    </button>
                                                </div>
                                                <input type="number" class="form-control" v-model="numbers[key]"
                                                    @click="selectedKey = key" @change="checkTickets">
                                            </div>
                                        </template>
                                    </div>
                                    <button class="btn btn-success">
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
                ticketnums: []
            }
        },
        mounted() {
            Event.listen('hideAlert', () => {
                this.notification.show = false;
                this.notification.message = '';
            })
        },
        methods: {
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
                var data = {name: this.name, number: this.numbers[this.selectedKey]};

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

            checkTickets(e) {
                console.log(e);
                axios.get(`/api/check?number=${e.target.value}`).then((res) => {
                    console.log(res.data);
                    if (!res.data.success) {
                        this.notify(res.data.message, "danger");
                    }
                });
            }
        }
    }
</script>
