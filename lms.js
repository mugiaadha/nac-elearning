import http, { CookieJar } from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
    stages: [
        { duration: '1m', target: 50 },   // warm up
        { duration: '1m', target: 100 },  // load naik
        { duration: '2m', target: 200 },  // moderate load
        { duration: '10m', target: 200 },  // moderate load
        // { duration: '2m', target: 500 },  // heavy load
        // { duration: '2m', target: 800 },  // stress level 1
        // { duration: '2m', target: 1000 }, // stress level 2
        { duration: '3m', target: 0 },    // cool down
    ],
};

// 🌿 Konstanta URL
const BASE_URL = 'http://localhost:8000';
const LOGIN_URL = `${BASE_URL}/login`;
const DASHBOARD_URL = `${BASE_URL}/dashboard`;

export default function () {
    const jar = new CookieJar();

    // 1️⃣ Ambil halaman login untuk dapat CSRF token & cookies
    const loginPage = http.get(LOGIN_URL, { jar: jar });

    const csrfTokenMatch = loginPage.body.match(/name="_token" value="([^"]+)"/);
    const csrfToken = csrfTokenMatch ? csrfTokenMatch[1] : null;

    check(csrfToken, {
        '✅ CSRF token extracted': (token) => token !== null,
    });

    // 2️⃣ POST login menggunakan CSRF token
    const payload = {
        _token: csrfToken,
        email: 'user@gmail.com',
        password: '123',
    };

    const headers = {
        'Content-Type': 'application/x-www-form-urlencoded',
    };

    const loginRes = http.post(LOGIN_URL, payload, {
        headers: headers,
        jar: jar,
        redirects: 0, // agar tetap dapat 302
    });

    check(loginRes, {
        '✅ Login returned 302 redirect': (r) => r.status === 302,
        '✅ Login redirected somewhere': (r) => r.headers.Location !== undefined,
    });

    sleep(5); // simulasi delay user

    // 3️⃣ GET halaman dashboard setelah login
    const dashRes = http.get(DASHBOARD_URL, { jar: jar });

    check(dashRes, {
        '✅ Dashboard returned 200': (r) => r.status === 200,
        '✅ Dashboard contains keyword': (r) => r.body.includes('Dashboard'),
    });

    sleep(5); // simulasi user membaca dashboard
}