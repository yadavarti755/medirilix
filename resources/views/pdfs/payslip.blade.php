<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payslip</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
        }

        .mb-0 {
            margin-bottom: 0px !important;
        }

        .mb-2 {
            margin-bottom: 10px !important;
        }

        .mb-3 {
            margin-bottom: 15px !important;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 8px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 9px 10px;
            vertical-align: top;
        }

        .center {
            text-align: center;
        }

        .no-border {
            border: none;
        }

        .header-section td {
            border: none;
        }

        .heading {
            font-weight: bold;
            text-align: center;
        }

        h1 {
            font-size: 32px;
            font-weight: bold;
            line-height: 0px !important;
            margin-bottom: 0px;
        }

        h2 {
            font-size: 28px;
            line-height: 0px !important;
            margin-bottom: 0px;
        }

        h3 {
            font-size: 24px;
            line-height: 0px !important;
            margin-bottom: 0px;
        }

        h4 {
            font-size: 20px;
            line-height: 0px !important;
            margin-bottom: 0px;
        }

        h5 {
            font-size: 16px;
            line-height: 0px !important;
            margin-bottom: 0px;
        }

        h6 {
            font-size: 14px;
            line-height: 0px !important;
            margin-bottom: 0px;
        }

        .employee_details_table tr td,
        .employee_details_table tr th {
            border: unset !important;
            padding: 4px 10px;
        }

        .employee_details_table tr th {
            font-weight: bold;
            text-align: left;
        }

        .employee_earn_dedu_table {
            border: unset !important;
        }

        .employee_earn_dedu_table tr td,
        .employee_earn_dedu_table tr th {
            border: unset !important;
            padding: 4px 10px;
        }

        .employee_earn_dedu_table tr th {
            font-weight: bold;
            text-align: left;
        }

        .total_table,
        .total_table tr td,
        .total_table tr th {
            border: unset !important;
        }
    </style>
</head>

<body>
    <div class="center">
        <img src="./assets/images/logo-afhq.png" alt="Image" style="width: 100px;">
        <h4 class="mb-2" style="font-weight: 700;">
            GOVERNMENT OF INDIA
        </h4>
        <h4 class="mb-2">
            MINISTRY OF DEFENCE
        </h4>
        <h5 class="mb-2">
            Office of the JS & CAO
        </h5>
    </div>

    <hr style="margin: 14px 0px;">

    <h5 class="mb-3 center">
        <strong>Pay Slip: {{ \Carbon\Carbon::parse($payslip->ondate)->format('M Y') }}</strong>
    </h5>

    <table style="margin-bottom: 20px;" class="employee_details_table">
        <tr>
            <th>Emp. No.</th>
            <td>{{ $payslip->employee_no }}</td>
            <th>D.O.B</th>
            <td>{{ date('d-m-Y', strtotime($employee->dob)) ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Name</th>
            <td>{{ strtoupper($employee->user->name) ?? 'N/A' }}</td>
            <th>Designation</th>
            <td>{{ strtoupper($employee->designation) ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Office</th>
            <td>{{ $payslip->office }}</td>
            <th>PAN</th>
            <td>
                {{ $employee->pan ? str_repeat('X', strlen($employee->pan) - 4) . substr($employee->pan, -4) : 'XXXXX' }}
            </td>
        </tr>
        <tr>
            <th>Pay Group</th>
            <td>{{ $payslip->cao }}</td>
            <th>GPF/PRAN No.</th>
            <td>{{ $payslip->gpfno }}</td>
        </tr>
        <tr>
            <th>Pay Level</th>
            <td>{{ $payslip->pscl_code }}</td>
            <th>Bank A/c</th>
            <td>{{ $payslip->accountno }}</td>
        </tr>
        <tr>
            <th>IFSC</th>
            <td colspan="3">{{ $payslip->ifsc }}</td>
        </tr>
    </table>

    <table width="100%" style="margin-bottom: 10px;">
        <tr>
            <td style=" vertical-align: top; width: 50%; padding: 0px;">
                {{-- Earnings Table --}}
                <table width="100%" class="employee_earn_dedu_table">
                    <thead>
                        <tr>
                            <th colspan="2" style="text-align: center;">EARNINGS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>BASIC PAY</td>
                            <td>{{ $payslip->basic_pay }}</td>
                        </tr>
                        <tr>
                            <td>DA</td>
                            <td>{{ $payslip->da }}</td>
                        </tr>
                        <tr>
                            <td>HRA</td>
                            <td>{{ $payslip->hra }}</td>
                        </tr>
                        <tr>
                            <td>TPT + DA</td>
                            <td>{{ $payslip->tpt }}</td>
                        </tr>
                        @if ($payslip->eh2)
                        <tr>
                            <td>{{ $payslip->eh2 }}</td>
                            <td>{{ $payslip->ev2 }}</td>
                        </tr>
                        @endif
                        @if ($payslip->eh3)
                        <tr>
                            <td>{{ $payslip->eh3 }}</td>
                            <td>{{ $payslip->ev3 }}</td>
                        </tr>
                        @endif
                        @if ($payslip->eh4)
                        <tr>
                            <td>{{ $payslip->eh4 }}</td>
                            <td>{{ $payslip->ev4 }}</td>
                        </tr>
                        @endif
                        @if ($payslip->eh5)
                        <tr>
                            <td>{{ $payslip->eh5 }}</td>
                            <td>{{ $payslip->ev5 }}</td>
                        </tr>
                        @endif
                        @if ($payslip->cpf_govt_contrbn)
                        <tr>
                            <td>NPS/UPS Govt contribution</td>
                            <td>{{ $payslip->cpf_govt_contrbn }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </td>

            <td style="vertical-align: top; width: 50%; padding: 0px;">
                {{-- Deductions Table --}}
                <table width="100%" class="employee_earn_dedu_table">
                    <thead>
                        <tr>
                            <th colspan="2" style="text-align: center;">DEDUCTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>GPF/NPS/UPS subs</td>
                            <td>{{ $payslip->gpf_subs }}</td>
                        </tr>
                        <tr>
                            <td>CGHS</td>
                            <td>{{ $payslip->cghs }}</td>
                        </tr>
                        <tr>
                            <td>CGEIS</td>
                            <td>{{ $payslip->cgies }}</td>
                        </tr>
                        @if ($payslip->income_tax)
                        <tr>
                            <td>Income Tax</td>
                            <td>{{ $payslip->income_tax }}</td>
                        </tr>
                        @endif
                        @if ($payslip->edu_cess)
                        <tr>
                            <td>CESS</td>
                            <td>{{ $payslip->edu_cess }}</td>
                        </tr>
                        @endif
                        @if ($payslip->dh1)
                        <tr>
                            <td>{{ $payslip->dh1 }}</td>
                            <td>
                                {{ $payslip->dv1 }}
                                &nbsp;&nbsp;
                                @if($payslip->inst1)
                                ({{ $payslip->inst1 }})
                                @endif
                            </td>
                        </tr>
                        @endif
                        @if ($payslip->dh2)
                        <tr>
                            <td>{{ $payslip->dh2 }}</td>
                            <td>
                                {{ $payslip->dv2 }}
                                &nbsp;&nbsp;
                                @if($payslip->inst2)
                                ({{ $payslip->inst2 }})
                                @endif
                            </td>
                        </tr>
                        @endif
                        @if ($payslip->dh3)
                        <tr>
                            <td>{{ $payslip->dh3 }}</td>
                            <td>
                                {{ $payslip->dv3 }}
                                &nbsp;&nbsp;
                                @if($payslip->inst3)
                                ({{ $payslip->inst3 }})
                                @endif
                            </td>
                        </tr>
                        @endif
                        @if ($payslip->dh4)
                        <tr>
                            <td>{{ $payslip->dh4 }}</td>
                            <td>
                                {{ $payslip->dv4 }}
                                &nbsp;&nbsp;
                                @if($payslip->inst4)
                                ({{ $payslip->inst4 }})
                                @endif
                            </td>
                        </tr>
                        @endif
                        @if ($payslip->dh5)
                        <tr>
                            <td>{{ $payslip->dh5 }}</td>
                            <td>
                                {{ $payslip->dv5 }}
                                &nbsp;&nbsp;
                                @if($payslip->inst5)
                                ({{ $payslip->inst5 }})
                                @endif
                            </td>
                        </tr>
                        @endif
                        @if ($payslip->dh6)
                        <tr>
                            <td>{{ $payslip->dh6 }}</td>
                            <td>
                                {{ $payslip->dv6 }}
                                &nbsp;&nbsp;
                                @if($payslip->inst6)
                                ({{ $payslip->inst6 }})
                                @endif
                            </td>
                        </tr>
                        @endif
                        @if ($payslip->dh7)
                        <tr>
                            <td>{{ $payslip->dh7 }}</td>
                            <td>
                                {{ $payslip->dv7 }}
                                &nbsp;&nbsp;
                                @if($payslip->inst7)
                                ({{ $payslip->inst7 }})
                                @endif
                            </td>
                        </tr>
                        @endif
                        @if ($payslip->cpf_govt_contrbn)
                        <tr>
                            <td>NPS/UPS Govt contribution</td>
                            <td>{{ $payslip->cpf_govt_contrbn }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <table class="total_table">
        <tbody>
            <tr>
                <td width="25%"><strong>GROSS PAY</strong></td>
                <td width="25%">{{ $payslip->gross_earn }}</td>
                <td width="25%"><strong>DEDUCTIONS</strong></td>
                <td width="25%">{{ $payslip->pymf_gross_deduct }}</td>
            </tr>
        </tbody>
    </table>

    <h6 class="mb-3">
        NET PAY: {{ $payslip->pymf_net_pay }}
    </h6>

    <hr>

    @if($payslip->remks && $payslip->remks != 0)
    <p style="text-align: center; margin-bottom: 5px;">
        {{ $payslip->remks }}
    </p>
    @endif
    <p class="mb-0" style="text-align: center;">
        This is a computer generated salary slip and does not require signature and stamp.
    </p>

</body>

</html>