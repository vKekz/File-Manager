import { UserDto } from "../dtos/user.dto";

export interface SessionResponse {
  userDto: UserDto;
  accessToken: string;
}
