export interface SearchVideosResult {
    id: string,
    user_id: string,
    s3_key: string,
    title: string,
    description: string,
    likes: number,
    is_banned: boolean,
    created_at: string,
    updated_at: string
}