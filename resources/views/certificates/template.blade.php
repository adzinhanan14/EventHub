<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 0; }
    * { box-sizing: border-box; }
    html, body {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        font-family: 'Helvetica', 'Arial', sans-serif;
        background: #ffffff;
    }
    .certificate {
        position: absolute;
        top: 0; left: 0; bottom: 0; right: 0;
        overflow: hidden;
        background: #ffffff;
        color: #333333;
    }

    /* Gold Frame Border */
    .gold-frame {
        position: absolute;
        top: 30px; bottom: 30px; left: 30px; right: 30px;
        border: 2px solid #d3ad5b;
        z-index: 1;
    }

    /* Corner Shapes Z-index */
    .corner-shape { position: absolute; z-index: 2; }

    /* Top Left Corner */
    .tl-navy-v { top: 0; left: 0; width: 45px; height: 260px; background-color: #152c4e; }
    .tl-navy-h { top: 0; left: 0; width: 260px; height: 45px; background-color: #152c4e; }
    .tl-gold-v { top: 55px; left: 55px; width: 12px; height: 180px; background-color: #d3ad5b; border-radius: 0 0 4px 4px;}
    .tl-gold-h { top: 55px; left: 55px; width: 180px; height: 12px; background-color: #d3ad5b; border-radius: 0 4px 4px 0;}
    .tl-gray-arc { top: 67px; left: 67px; width: 140px; height: 140px; background-color: #e0e4eb; border-bottom-right-radius: 100%; }

    /* Bottom Right Corner */
    .br-navy-v { bottom: 0; right: 0; width: 45px; height: 260px; background-color: #152c4e; }
    .br-navy-h { bottom: 0; right: 0; width: 260px; height: 45px; background-color: #152c4e; }
    .br-gold-v { bottom: 55px; right: 55px; width: 12px; height: 180px; background-color: #d3ad5b; border-radius: 4px 4px 0 0;}
    .br-gold-h { bottom: 55px; right: 55px; width: 180px; height: 12px; background-color: #d3ad5b; border-radius: 4px 0 0 4px;}
    .br-gray-arc { bottom: 67px; right: 67px; width: 140px; height: 140px; background-color: #e0e4eb; border-top-left-radius: 100%; }

    /* Top Right & Bottom Left Geometric Decos */
    .tr-deco-1 { top: 15px; right: 15px; width: 70px; height: 70px; border-top: 2px solid #d3ad5b; border-right: 2px solid #d3ad5b; }
    .tr-deco-2 { top: 40px; right: 40px; width: 35px; height: 35px; border-top: 2px solid #d3ad5b; border-right: 2px solid #d3ad5b; }
    
    .bl-deco-1 { bottom: 15px; left: 15px; width: 70px; height: 70px; border-bottom: 2px solid #d3ad5b; border-left: 2px solid #d3ad5b; }
    .bl-deco-2 { bottom: 40px; left: 40px; width: 35px; height: 35px; border-bottom: 2px solid #d3ad5b; border-left: 2px solid #d3ad5b; }

    /* Content Styling */
    .content {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 10;
        text-align: center;
        padding-top: 70px;
    }

    .title-sertifikat {
        font-family: 'Helvetica', 'Arial', sans-serif;
        font-weight: bold;
        font-size: 54px;
        color: #123053;
        letter-spacing: 2px;
        line-height: 1;
        margin: 0;
        text-transform: uppercase;
    }
    .title-penghargaan {
        font-family: 'Helvetica', 'Arial', sans-serif;
        font-weight: bold;
        font-size: 26px;
        color: #123053;
        letter-spacing: 2px;
        margin-top: 5px;
        text-transform: uppercase;
    }

    .presented-to {
        margin-top: 35px;
        font-size: 15px;
        color: #333333;
    }

    .name-wrapper {
        height: 110px;
        display: block;
        margin-top: 5px;
    }

    .name {
        font-family: 'Georgia', 'Times New Roman', serif;
        font-size: 40px;
        font-weight: bold;
        color: #be9436;
        line-height: 1.2;
        text-transform: uppercase;
        margin: 0;
        width: 80%;
        margin: 0 auto;
    }

    .name-line {
        width: 600px;
        height: 2px;
        background-color: #8fa0b5;
        margin: 5px auto 25px;
    }

    .desc {
        font-size: 14px;
        color: #444444;
        line-height: 1.5;
        max-width: 650px;
        margin: 0 auto;
    }

    .footer {
        width: 100%;
        margin-top: 15px;
    }
    
    .footer table {
        width: 90%;
        margin: 0 auto;
        table-layout: fixed;
    }
    
    .footer td {
        vertical-align: bottom;
        text-align: center;
        padding-bottom: 10px;
    }

    .signature-container {
        height: 60px;
    }
    
    .signature-text {
        font-family: 'Georgia', 'Times New Roman', serif;
        font-style: italic;
        font-size: 26px;
        color: #0f1c3f;
        line-height: 1;
        margin-bottom: 5px;
    }

    .sign-line {
        width: 80%;
        height: 2px;
        background-color: #8fa0b5;
        margin: 0 auto 5px;
    }
    .sign-name {
        font-size: 13px;
        font-weight: bold;
        color: #123053;
        margin-bottom: 2px;
        text-transform: uppercase;
        font-family: 'Helvetica', 'Arial', sans-serif;
    }
    .sign-title {
        font-size: 12px;
        color: #555555;
    }

    .seal-wrap {
        width: 100%;
        text-align: center;
    }
    .seal {
        width: 50px; 
        height: 62px; 
        margin: 0 auto;
        background-color: #dcaa3a;
        border: 2px solid #b8860b;
        border-radius: 4px 4px 30px 30px;
        position: relative;
    }
    
    .meta-id {
        position: absolute;
        bottom: 15px;
        left: 0;
        width: 100%;
        text-align: center;
        font-size: 9px;
        color: #a0aab8;
        z-index: 10;
    }
</style>
</head>
<body>
    <div class="certificate">
        <!-- Center Gold Frame -->
        <div class="gold-frame"></div>
        
        <!-- Corner Decorations -->
        <div class="corner-shape tl-navy-v"></div>
        <div class="corner-shape tl-navy-h"></div>
        <div class="corner-shape tl-gold-v"></div>
        <div class="corner-shape tl-gold-h"></div>
        <div class="corner-shape tl-gray-arc"></div>

        <div class="corner-shape br-navy-v"></div>
        <div class="corner-shape br-navy-h"></div>
        <div class="corner-shape br-gold-v"></div>
        <div class="corner-shape br-gold-h"></div>
        <div class="corner-shape br-gray-arc"></div>

        <div class="corner-shape tr-deco-1"></div>
        <div class="corner-shape tr-deco-2"></div>
        <div class="corner-shape bl-deco-1"></div>
        <div class="corner-shape bl-deco-2"></div>

        <!-- Main Content -->
        <div class="content">
            <h1 class="title-sertifikat">SERTIFIKAT</h1>
            <h2 class="title-penghargaan">PENGHARGAAN</h2>
            
            <p class="presented-to">Dengan bangga diberikan kepada :</p>
            
            <div class="name-wrapper">
                <h1 class="name">{{ $title ?? $name ?? 'Peserta' }}</h1>
            </div>
            <div class="name-line"></div>
            
            <p class="desc">
                Sertifikat ini dianugerahkan atas partisipasi dan kehadiran penuh dalam rangkaian acara<br>
                <strong>{{ $eventTitle ?? 'Nama Event' }}</strong><br>
                yang diselenggarakan secara sukses pada tanggal {{ isset($eventDate) ? \Carbon\Carbon::parse($eventDate)->translatedFormat('d F Y') : 'Tanggal' }} oleh {{ $organizationName ?? 'Organisasi' }}.
            </p>

            <div class="footer">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="30%">
                            <div class="signature-container">
                                <div class="signature-text">M. Suyanto</div>
                            </div>
                            <div class="sign-line"></div>
                            <div class="sign-name">Prof. Dr. M. Suyanto, M.M.</div>
                            <div class="sign-title">Rektor Amikom</div>
                        </td>
                        
                        <td width="40%">
                            <div class="seal-wrap">
                                <div class="seal"></div>
                            </div>
                        </td>
                        
                        <td width="30%">
                            <div class="signature-container">
                                <div class="signature-text">{{ $organizationName ?? 'Organisasi' }}</div>
                            </div>
                            <div class="sign-line"></div>
                            <div class="sign-name">Ketua Panitia</div>
                            <div class="sign-title">{{ $organizationName ?? 'Penyelenggara Event' }}</div>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="meta-id">Order ID: {{ $orderId ?? 'N/A' }} &middot; Diterbitkan oleh AmikomEventHub</div>
        </div>
    </div>
</body>
</html>