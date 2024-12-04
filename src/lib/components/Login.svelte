<script lang="ts">
     import { auth } from '../stores/auth';
   
     let username = '';
     let password = '';
     let error = '';
     let isLoading = false;
   
     async function login() {
       error = '';
       isLoading = true;
   
       try {
         const response = await fetch('http://localhost/Library-Management/backend/api.php/login', {
           method: 'POST',
           headers: {
             'Content-Type': 'application/json',
           },
           body: JSON.stringify({ username, password }),
         });
   
         const data = await response.json();
   
         if (response.ok) {
           auth.login({ id: data.id, username: data.username, role: data.role });
         } else {
           error = data.error || 'Login failed. Please try again.';
         }
       } catch (err) {
         console.error('Login error:', err);
         error = 'An unexpected error occurred. Please try again later.';
       } finally {
         isLoading = false;
       }
     }
   </script>
   
   <form on:submit|preventDefault={login} class="space-y-4 max-w-md mx-auto mt-8">
     <h2 class="text-2xl font-bold mb-4">Login</h2>
     {#if error}
       <p class="text-red-500">{error}</p>
     {/if}
     <div>
       <label for="username" class="block mb-1">Username</label>
       <input
         id="username"
         bind:value={username}
         type="text"
         required
         class="w-full p-2 border rounded"
         disabled={isLoading}
       />
     </div>
     <div>
       <label for="password" class="block mb-1">Password</label>
       <input
         id="password"
         bind:value={password}
         type="password"
         required
         class="w-full p-2 border rounded"
         disabled={isLoading}
       />
     </div>
     <button 
       type="submit" 
       class="w-full bg-blue-500 text-white p-2 rounded disabled:bg-blue-300"
       disabled={isLoading}
     >
       {isLoading ? 'Logging in...' : 'Login'}
     </button>
   </form>
   
   