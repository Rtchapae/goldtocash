import { get, post, del } from './client'

export const getUserNotes = async (userId) => {
	return get(`/admin/users/note/query?userId=${userId}`)
}

export const saveUserNote = async ({ userId, noteId, text }) => {
	return post('/admin/users/note/update-or-create', {
		userId: userId || null,
		noteId: noteId || null,
		text: text || '',
	})
}

export const deleteUserNote = async (noteId) => {
	return del(`/admin/users/note/delete?id=${noteId}`)
}


