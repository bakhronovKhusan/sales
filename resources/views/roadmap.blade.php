<style>
    @font-face {
        font-family: "CircularStd";
        src: url('{{url('/assets/fonts/pdf/CircularStd-Black.woff')}}') format('truetype');
        src: url('{{url('/assets/fonts/pdf/CircularStd-Black.woff2')}}') format('truetype');
        font-display: swap;
        font-weight: 900;
        font-style: normal;
    }

    @font-face {
        font-family: "CircularStd";
        src: url('{{url('/assets/fonts/pdf/CircularStd-Bold.woff')}}') format('truetype');
        src: url('{{url('/assets/fonts/pdf/CircularStd-Bold.woff2')}}') format('truetype');
        font-display: swap;
        font-weight: 700;
        font-style: normal;
    }

    @font-face {
        font-family: "CircularStd";
        src: url('{{url('/assets/fonts/pdf/CircularStd-Medium.woff')}}') format('truetype');
        src: url('{{url('/assets/fonts/pdf/CircularStd-Medium.woff2')}}') format('truetype');
        font-display: swap;
        font-weight: 500;
        font-style: normal;
    }
    body {
        background: white;
        font-size: 10px;
        font-family: 'CircularStd';
    }
    /*page[size='A4'] {*/
    /*    background: white;*/
    /*    width: 21cm;*/
    /*    height: 29.7cm;*/
    /*    display: block;*/
    /*    margin: 0 auto;*/
    /*    margin-bottom: 0.5cm;*/
    /*    box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);*/
    /*}*/
    /*@media print {*/
    /*    body, page[size='A4'] {*/
    /*        margin: 0;*/
    /*        box-shadow: 0;*/
    /*    }*/
    /*}*/

</style>
<div style="max-width: 563px;
            margin: 30px auto;
            width: 100%;
            border-radius: 22px;
            overflow: hidden;
            border: 0.5px solid #eef0f8;
            background: #fff;position: relative">
    <header style="display: inline-block;">
        <div style="border-radius: 15px;
                    background: #0d99ff;
                    box-shadow: 0px 0px 10px 0px #dee5f3;
                    padding: 20px;
                    margin: 20px;
                    display: inline-block;">
            <div  style="display: flex;gap: 10px;">
                <a href=""><img src="https://sales-api.cambridgeonline.uz/img/logo.png" alt="logo"></a>
                <p style="color: #fff;font-size: 12px;font-style: normal;font-weight: 700;line-height: 130%;">Study Plan <br> for Student</p>
            </div>
            <h2 style="padding-top: 15px;color: #fff;font-size: 12px;font-style: normal;font-weight: 700;line-height: 130%;letter-spacing: -0.06px;text-transform: capitalize;">
                    {{ $student->name }} <br>
                            {{ $student->surname }}</h2>
            <h1 style="color: #fff;font-size: 32px;font-style: normal;font-weight: 700;line-height: 130%;letter-spacing: -0.16px;">{{ $level->name }}</h1>
            <h3 style="color: #fff;font-size: 12px;font-style: normal;font-weight: 300;line-height: 130%;letter-spacing: -0.16px;margin-bottom: 20px;">Correct Answers: 14</h3>
            <img src="https://sales-api.cambridgeonline.uz/img/Graph.png" alt="graph" style="width: 100%;max-width: 130px">
        </div>
        <div style="padding: 20px;border-left: 1px solid #EEF0F8;width: 315px;position: absolute;top: 0;right: 0">
            <img src="https://sales-api.cambridgeonline.uz/img/Chart.png" alt="chart" style="width: 100%;">
        </div>
    </header>
    <section style="border-top: 1px solid #EEF0F8;padding: 30px;">
        <img src="https://sales-api.cambridgeonline.uz/img/Group2.png" alt="roadmap" style="width: 100%;">
    </section>
</div>
