export interface GetCurrentUserResponse {
    id: string;
    first_name: string;
    last_name: string;
    email: string;
    email_verified_at: string | null;
    username: string;
    created_at: string;
    updated_at: string;
    suspended_until: string | null;
    permanently_banned_at: string | null;
}
