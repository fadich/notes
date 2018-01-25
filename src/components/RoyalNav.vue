<template>
    <nav>
        <div class="wrap">
            <div class="left nav-item">
                <div class="logo">
                    <a href="/" class="uppercase">Notes</a>
                </div>
            </div>
            <div class="right nav-item">
                <div class="logout">
                    <a href="https://www.auth.royallib.pw/auth/sign-out">Logout</a>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
  export default {}
</script>

<style lang="scss">
    nav {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 0 30px;
        border-bottom: solid 1px #E0E0E0;

        .uppercase {
            text-transform: uppercase;
        }

        .wrap {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: row;
            width: 100%;
            height: 60px;
            vertical-align: middle;

            .left {
                justify-self: flex-start;
            }

            .right {
                justify-self: flex-end;
            }

            .nav-item {
                height: 100%;
                padding: 10px;
                display: flex;
                justify-content: center;
                align-items: center;
                transition: 0.3s;

                &:hover {
                    background: #EFEFEF;
                    transition: 0.3s;
                }

                * {
                    padding: 25% 0;
                }
            }

            .logo, .logout {
                a {
                    color: black;
                    text-decoration: none;
                }

                &:hover {
                    color: black;
                    text-decoration: none;
                }
                &:focus {
                    color: black;
                    text-decoration: none;
                }
            }
        }
    }
</style>
