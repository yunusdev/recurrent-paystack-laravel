<template>
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center mb-5">

                    <h3 class="mt-5 text-center mb-3">N{{user.wallet_balance}}</h3>
                    <div class="input-group mt-2 mb-3">
                        <input
                          placeholder="1000"
                          v-model="amount"
                          type="text"
                          class="form-control"
                        >
                        <div class="input-group-append">

                            <paystack
                                class="btn font-weight-bold btn-secondary"
                                v-if="!user.card"
                                :amount="amount * 100"
                                :email="user.email"
                                :paystackkey="paystack_pb_key"
                                :reference="'ref-' + Date.now()"
                                :callback="topUp"
                                :close="close"
                                :embed="false"
                                :disabled="amount === ''"
                            >
                                Top Up
                            </paystack>
                            <button v-else @click="topUp" class="btn text-uppercase font-weight-bold btn-warning">
                                Top Up
                            </button>
                        </div>
                    </div>

                    <Card v-if="user.card" :card="user.card"/>

                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Wallet History</div>
                <div class="card-body">
                    <table class="table table-striped border">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Currency</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr :key="history.id" v-for="history, key in wallet_histories">
                            <td>{{key + 1}}</td>
                            <td>{{ history.currency }}</td>
                            <td>{{ history.amount }}</td>
                            <td>
                                <span class="badge font-weight-bold text-uppercase px-2 py-2" :class="history.status ==='success' ? 'badge-warning' : 'badge-danger'">
                                {{ history.status }}
                                </span>
                            </td>
                            <td>{{ history.formatted_date }}</td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import paystack from 'vue-paystack'
import Axios from 'axios'
import Card from './CreditCard'

export default {
    name: "Payment",

    props: ['raw_user', 'paystack_pb_key', 'raw_wallet_histories'],

    components: { paystack, Card },

    mounted(){


        this.$on('delete_card', () => {

            this.user.card = null
        })
    },

    data(){

        return {

            amount: '1000',
            user: this.raw_user,
            wallet_histories: JSON.parse(this.raw_wallet_histories)

        }

    },

    methods: {

        topUp(res){
            this.$loading(true)
            Axios.post('/pay/' + res.reference, {
                amount: this.amount
            }).then(res => {
                const data = res.data;
                if (!this.user.card) this.user.card = data.card;
                this.user.wallet_balance = parseInt(this.user.wallet_balance) +  parseInt(data.history.amount)
                this.wallet_histories.unshift(data.history)
                this.$loading(false)
                this.notifSuceess('Payment completed successfully!')


            }).catch(err => {
                this.$loading(false)
                this.notifError('An error occurred. Pls try again')

            })

        },

        close(){


        }



    }

}
</script>

<style scoped type="text/css">


</style>
