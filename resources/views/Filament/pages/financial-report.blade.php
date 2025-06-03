@use('App\Enums\PaymentStatuses')
<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Bevételek összehasonlítása</h2>
                <div class="space-y-4">
                    <div>
                        <h3 class="font-medium text-gray-700 dark:text-gray-300">Kifizetett bevétel</h3>
                        <div class="flex justify-between mt-1">
                            <div class="text-2xl font-bold text-green-600">
                                {{ Number::currency(\App\Models\Income::whereStatus(PaymentStatuses::PAID)->sum('amount'), 'HUF', 'hu', 0) }}
                                Ft
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ \App\Models\Income::whereStatus(PaymentStatuses::PAID)->count() }} tétel
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-medium text-gray-700 dark:text-gray-300">Kifizetetlen bevétel</h3>
                        <div class="flex justify-between mt-1">
                            <div class="text-2xl font-bold text-amber-500">
                                {{ Number::currency(\App\Models\Income::whereStatus(PaymentStatuses::DRAFT)->sum('amount'), 'HUF', 'hu', 0) }}
                                Ft
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ \App\Models\Income::whereStatus(PaymentStatuses::DRAFT)->count() }} tétel
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <h3 class="font-medium text-gray-700 dark:text-gray-300">Összes bevétel</h3>
                        <div class="flex justify-between mt-1">
                            <div class="text-2xl font-bold">
                                {{ Number::currency(\App\Models\Income::sum('amount'), 'HUF', 'hu', 0) }}
                                Ft
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Kiadások összehasonlítása</h2>
                <div class="space-y-4">
                    <div>
                        <h3 class="font-medium text-gray-700 dark:text-gray-300">Kifizetett kiadások</h3>
                        <div class="flex justify-between mt-1">
                            <div class="text-2xl font-bold text-red-600">
                                {{ Number::currency(\App\Models\Expense::whereStatus(PaymentStatuses::PAID)->sum('amount'), 'HUF', 'hu', 0) }}
                                Ft
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ \App\Models\Expense::whereStatus(PaymentStatuses::PAID)->count() }} tétel
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-medium text-gray-700 dark:text-gray-300">Kifizetetlen kiadások</h3>
                        <div class="flex justify-between mt-1">
                            <div class="text-2xl font-bold text-orange-500">
                                {{ Number::currency(\App\Models\Expense::whereStatus(PaymentStatuses::DRAFT)->sum('amount'), 'HUF', 'hu', 0) }}
                                Ft
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ \App\Models\Expense::whereStatus(PaymentStatuses::DRAFT)->count() }} tétel
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <h3 class="font-medium text-gray-700 dark:text-gray-300">Összes kiadás</h3>
                        <div class="flex justify-between mt-1">
                            <div class="text-2xl font-bold">
                                {{ Number::currency(\App\Models\Expense::sum('amount'), 'HUF', 'hu', 0) }}
                                Ft
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Pénzügyi egyenleg</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="font-medium text-gray-700 dark:text-gray-300">Összes bevétel</h3>
                    <div class="text-2xl font-bold text-green-600 mt-1">
                        {{ Number::currency(\App\Models\Income::sum('amount'), 'HUF', 'hu', 0) }}
                    </div>
                </div>

                <div>
                    <h3 class="font-medium text-gray-700 dark:text-gray-300">Összes kiadás</h3>
                    <div class="text-2xl font-bold text-red-600 mt-1">
                        {{ Number::currency(\App\Models\Expense::sum('amount'), 'HUF', 'hu', 0) }}
                    </div>
                </div>

                <div>
                    <h3 class="font-medium text-gray-700 dark:text-gray-300">Nettó egyenleg</h3>
                    <div
                        class="text-2xl font-bold {{ \App\Models\Income::sum('amount') > \App\Models\Expense::sum('amount') ? 'text-green-600' : 'text-red-600' }} mt-1">
                        {{ Number::currency(\App\Models\Income::sum('amount') - \App\Models\Expense::sum('amount'), 'HUF', 'hu', 0) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
