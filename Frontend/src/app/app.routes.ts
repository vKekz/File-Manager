import { Routes } from "@angular/router";
import { HomeComponent } from "./components/home/home.component";
import { LoginFormComponent } from "./components/login-form/login-form.component";
import { SignupFormComponent } from "./components/signup-form/signup-form.component";
import { AccountPageComponent } from "./components/account-page/account-page.component";
import { AuthGuard } from "../guards/auth.guard";
import { SessionGuard } from "../guards/session.guard";
import { StoragePageComponent } from "./components/storage-page/storage-page.component";
import { APP_ROUTES } from "../constants/route.constants";

export const routes: Routes = [
  { path: APP_ROUTES.home.path, component: HomeComponent, pathMatch: "full" },
  { path: APP_ROUTES.login.path, component: LoginFormComponent, pathMatch: "full", canActivate: [SessionGuard] },
  { path: APP_ROUTES.signup.path, component: SignupFormComponent, pathMatch: "full", canActivate: [SessionGuard] },
  { path: APP_ROUTES.account.path, component: AccountPageComponent, pathMatch: "full", canActivate: [AuthGuard] },
  { path: APP_ROUTES.storage.path, component: StoragePageComponent, pathMatch: "full", canActivate: [AuthGuard] },
];
