import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, tap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class EnsanApiService {
  private baseUrl = 'http://your-server-ip/api';
  private readonly tokenStorageKey = 'anasen_token';
  private readonly userStorageKey = 'anasen_user';

  constructor(private http: HttpClient) { }

  private getHeaders() {
    const token = localStorage.getItem(this.tokenStorageKey);
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
  }

  private persistAuthSession(response: any, fallbackName?: string) {
    if (response?.token) {
      localStorage.setItem(this.tokenStorageKey, response.token);
    }

    if (response?.user) {
      const normalizedName = fallbackName?.trim();
      const storedUser = normalizedName
        ? { ...response.user, name: normalizedName }
        : response.user;

      localStorage.setItem(this.userStorageKey, JSON.stringify(storedUser));
    }
  }

  private clearAuthSession() {
    localStorage.removeItem(this.tokenStorageKey);
    localStorage.removeItem(this.userStorageKey);
  }

  // --- Auth ---
  login(phone: string, name?: string): Observable<any> {
    const normalizedName = name?.trim();
    const payload: any = { phone };

    if (normalizedName) {
      payload.name = normalizedName;
    }

    return this.http.post(`${this.baseUrl}/auth/login`, payload);
  }

  verifyOtp(phone: string, otp: string, name?: string): Observable<any> {
    const normalizedName = name?.trim();
    const payload: any = { phone, otp };

    if (normalizedName) {
      payload.name = normalizedName;
    }

    return this.http.post(`${this.baseUrl}/auth/verify-otp`, payload).pipe(
      tap((response) => this.persistAuthSession(response, normalizedName))
    );
  }

  logout(): Observable<any> {
    return this.http.post(`${this.baseUrl}/auth/logout`, {}, { headers: this.getHeaders() }).pipe(
      tap(() => this.clearAuthSession())
    );
  }

  // --- Donations ---
  createDonation(data: any): Observable<any> {
    return this.http.post(`${this.baseUrl}/donations`, data, { headers: this.getHeaders() });
  }

  uploadProof(donationId: number, file: File): Observable<any> {
    const formData = new FormData();
    formData.append('donation_id', donationId.toString());
    formData.append('proof_image', file);

    return this.http.post(`${this.baseUrl}/donations/upload-proof`, formData, { headers: this.getHeaders() });
  }

  // --- Admin ---
  getUsers(): Observable<any> {
    return this.http.get(`${this.baseUrl}/admin/users`, { headers: this.getHeaders() });
  }

  getDonorFile(userId: number): Observable<any> {
    return this.http.get(`${this.baseUrl}/admin/users/${userId}`, { headers: this.getHeaders() });
  }

  deleteUser(userId: number): Observable<any> {
    return this.http.delete(`${this.baseUrl}/admin/users/${userId}`, { headers: this.getHeaders() });
  }

  getPendingDonations(): Observable<any> {
    return this.http.get(`${this.baseUrl}/admin/donations`, { headers: this.getHeaders() });
  }

  verifyDonation(donationId: number): Observable<any> {
    return this.http.post(`${this.baseUrl}/admin/donations/verify`, { donation_id: donationId }, { headers: this.getHeaders() });
  }

  rejectDonation(donationId: number, reason: string): Observable<any> {
    return this.http.post(`${this.baseUrl}/admin/donations/reject`, { donation_id: donationId, reason }, { headers: this.getHeaders() });
  }
}
