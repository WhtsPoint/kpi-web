import React, { useState, useEffect } from 'react';
import axios from './axios.js';
import { motion, AnimatePresence } from 'framer-motion';

function App() {
    const [posts, setPosts] = useState([]);
    const [editingPost, setEditingPost] = useState(null);
    const [formData, setFormData] = useState({ title: '', author: '', text: '' });

    useEffect(() => {
        fetchPosts();
    }, []);

    const fetchPosts = async () => {
        try {
            const response = await axios.get('/api/posts?limit=10&offset=0');
            setPosts(response.data);
        } catch (error) {
            console.error('Error fetching posts:', error);
        }
    };

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });
    };

    const handleCreatePost = async () => {
        try {
            const response = await axios.post('/api/post', formData);
            setPosts([...posts, { ...formData, id: response.data.id }]);
            setFormData({ title: '', author: '', text: '' });
        } catch (error) {
            console.error('Error creating post:', error);
        }
    };

    const handleEditPost = (post) => {
        setEditingPost(post);
        setFormData({ title: post.title, author: post.author, text: post.text });
    };

    const handleUpdatePost = async () => {
        try {
            await axios.patch(`/api/post/${editingPost.id}`, {
                title: formData.title,
                text: formData.text,
            });
            setPosts(
                posts.map((post) =>
                    post.id === editingPost.id
                        ? { ...post, title: formData.title, text: formData.text }
                        : post
                )
            );
            setEditingPost(null);
            setFormData({ title: '', author: '', text: '' });
        } catch (error) {
            console.error('Error updating post:', error);
        }
    };

    const handleDeletePost = async (id) => {
        try {
            await axios.delete(`/api/post/${id}`);
            setPosts(posts.filter((post) => post.id !== id));
        } catch (error) {
            console.error('Error deleting post:', error);
        }
    };

    return (
        <div className="min-h-screen bg-gradient-to-r from-purple-500 to-indigo-500 p-8 text-white font-sans">
            <div className="max-w-3xl mx-auto">
                <h1 className="text-4xl font-bold mb-6 text-center">Post Editor</h1>

                {/* Form */}
                <motion.div
                    className="bg-white text-black p-6 rounded-lg shadow-lg mb-8"
                    initial={{ opacity: 0, y: -20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5 }}
                >
                    <input
                        type="text"
                        name="title"
                        placeholder="Title"
                        value={formData.title}
                        onChange={handleInputChange}
                        className="block w-full p-2 mb-4 border border-gray-300 rounded"
                    />
                    <input
                        type="text"
                        name="author"
                        placeholder="Author"
                        value={formData.author}
                        onChange={handleInputChange}
                        className="block w-full p-2 mb-4 border border-gray-300 rounded"
                        disabled={!!editingPost}
                    />
                    <textarea
                        name="text"
                        placeholder="Text"
                        value={formData.text}
                        onChange={handleInputChange}
                        className="block w-full p-2 mb-4 border border-gray-300 rounded"
                    ></textarea>
                    <button
                        onClick={editingPost ? handleUpdatePost : handleCreatePost}
                        className="w-full py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                    >
                        {editingPost ? 'Update Post' : 'Create Post'}
                    </button>
                    {editingPost && (
                        <button
                            onClick={() => setEditingPost(null)}
                            className="w-full mt-2 py-2 bg-gray-300 text-black rounded hover:bg-gray-400"
                        >
                            Cancel
                        </button>
                    )}
                </motion.div>

                {/* Posts List */}
                <AnimatePresence>
                    {posts.map((post) => (
                        <motion.div
                            key={post.id}
                            className="bg-white text-black p-6 rounded-lg shadow-lg mb-4"
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -20 }}
                            transition={{ duration: 0.4 }}
                        >
                            <h2 className="text-xl font-bold">{post.title}</h2>
                            <p className="text-sm text-gray-500 mb-4">By {post.author}</p>
                            <p className="mb-4">{post.text}</p>
                            <div className="flex justify-end space-x-2">
                                <button
                                    onClick={() => handleEditPost(post)}
                                    className="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                                >
                                    Edit
                                </button>
                                <button
                                    onClick={() => handleDeletePost(post.id)}
                                    className="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                                >
                                    Delete
                                </button>
                            </div>
                        </motion.div>
                    ))}
                </AnimatePresence>
            </div>
        </div>
    );
}

export default App;