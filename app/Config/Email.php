<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = 'gjqmaoslwj@naver.com';  // 발신 이메일 (네이버 이메일)
    public string $fromName   = 'Vegan Korea';            // 발신자 이름
    public string $recipients = '';                        // 수신자 이메일 (기본값은 빈 값)

    /**
     * The "user agent"
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    public string $protocol = 'smtp';

    /**
     * SMTP Server Hostname
     */
    public string $SMTPHost = 'smtp.naver.com';  // 네이버 SMTP 서버

    /**
     * SMTP Username
     */
    public string $SMTPUser = 'gjqmaoslwj@naver.com';  // 네이버 이메일 아이디 (SMTP 인증 이메일)

    /**
     * SMTP Password
     */
    public string $SMTPPass = '!Alcls1475';  // 네이버 로그인 비밀번호 또는 앱 비밀번호

    /**
     * SMTP Port
     */
    public int $SMTPPort = 587;  // TLS를 사용하는 경우 포트 587

    /**
     * SMTP Timeout (in seconds)
     */
    public int $SMTPTimeout = 5;

    /**
     * SMTP Encryption.
     *
     * @var string '', 'tls' or 'ssl'. 'tls' will issue a STARTTLS command
     *             to the server. 'ssl' means implicit SSL. Connection on port
     *             465 should set this to 'ssl'.
     */
    public string $SMTPCrypto = 'tls';  // TLS 사용

    /**
     * Enable word-wrap
     */
    public bool $wordWrap = true;

    /**
     * Character count to wrap at
     */
    public int $wrapChars = 76;

    /**
     * Type of mail, either 'text' or 'html'
     */
    public string $mailType = 'html';  // 이메일 내용 형식

    /**
     * Character set (utf-8, iso-8859-1, etc.)
     */
    public string $charset = 'UTF-8';

    /**
     * Whether to validate the email address
     */
    public bool $validate = true;

    /**
     * Email Priority. 1 = highest. 5 = lowest. 3 = normal
     */
    public int $priority = 3;

    /**
     * Newline character. (Use “\r\n” to comply with RFC 822)
     */
    public string $CRLF = "\r\n";

    /**
     * Newline character. (Use “\r\n” to comply with RFC 822)
     */
    public string $newline = "\r\n";

    /**
     * Enable BCC Batch Mode.
     */
    public bool $BCCBatchMode = false;

    /**
     * Number of emails in each BCC batch
     */
    public int $BCCBatchSize = 200;

    /**
     * Enable notify message from server
     */
    public bool $DSN = false;
}
